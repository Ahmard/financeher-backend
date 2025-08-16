<?php

namespace App\Http\Controllers;

use App\Enums\Statuses\PaymentStatus;
use App\Enums\SystemSettingDefinition;
use App\Enums\Types\PaymentPurpose;
use App\Exceptions\MaintenanceException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Helpers\PaymentHelper;
use App\Helpers\SettingHelper;
use App\Models\Payment;
use App\Models\Plan;
use App\Repositories\PaymentRepository;
use App\Services\PaymentService;
use App\Services\UserService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct(
        private readonly Responder      $responder,
        private readonly PaymentService $service,
    )
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        return $this->responder->datatableFilterable(
            builder: $this
                ->service
                ->repository
                ->queryBuilder()
                ->withSearch($this->getSearchQuery())
                ->filterByPayerId(Auth::id()),
            responseMessage: 'payment history retrieved'
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $payment = $this->service->repository->findRequiredById($id);
        return $this->responder->success(
            data: $payment,
            message: 'Payment info fetched'
        );
    }

    /**
     * @param string $reference
     * @return JsonResponse
     * @throws BindingResolutionException
     * @throws GuzzleException
     * @throws MaintenanceException
     * @throws ModelNotFoundException
     * @throws WarningException
     */
    public function verify(string $reference): JsonResponse
    {
        $result = $this->service->verifyTransaction(
            userId: Auth::id(),
            reference: $reference
        );

        if ($result->status == PaymentStatus::PENDING) {
            throw new WarningException('You are yet to make payment or payment is yet to be verified');
        }

        return $this->responder->success($result->payment);
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'customer_email' => 'required|email',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url'
        ]);

        try {
            $planId = SettingHelper::get(SystemSettingDefinition::ACTIVE_PLAN_ID);
            $plan = Plan::query()->findOrFail($planId);
            $user = Auth::user();

            $checkoutSession = Session::create([
                'payment_method_types' => ['card', 'klarna', 'revolut_pay'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp', // Changed to GBP based on your frontend
                        'product_data' => [
                            'name' => "FinanceHer {$plan->name} Plan",
                            'description' => "FinanceHer {$plan->name} ({$plan->billing_cycle}) subscription",
                        ],
                        'unit_amount' => $plan->price * 100, // Stripe uses pence for GBP
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $request->success_url . '&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->cancel_url,
                'customer_email' => $request->customer_email,
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'billing_cycle' => $plan->billing_cycle,
                ],
                'billing_address_collection' => 'auto',
                'phone_number_collection' => [
                    'enabled' => true,
                ],
            ]);

            return $this->responder->success(
                data: [
                    'checkout_url' => $checkoutSession->url,
                    'session_id' => $checkoutSession->id
                ],
                message: 'Checkout session created successfully'
            );

        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
            return $this->responder->error(
                message: 'Failed to create checkout session: ' . $e->getMessage(),
                code: 400
            );
        }
    }

    /**
     * Handle Stripe Webhook Events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $endpoint_secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event['data']['object']);
                break;
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object']);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event['data']['object']);
                break;
            default:
                Log::info('Received unknown Stripe webhook event type: ' . $event['type']);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Verify checkout session and create subscription
     */
    public function verifyCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $session = Session::retrieve($request->session_id);

            if ($session->payment_status === 'paid') {
                // Create subscription record if not already created
                $payment = $this->createSubscriptionFromSession($session);

                UserService::new()->markAsPaymentMade($payment['payer_id']);

                return $this->responder->success(
                    data: [
                        'payment_status' => $session->payment_status,
                        'customer_email' => $session->customer_email,
                        'amount_total' => $session->amount_total,
                    ],
                    message: 'Payment verified successfully'
                );
            }

            return $this->responder->error(
                message: 'Payment not completed',
                code: 400
            );

        } catch (\Exception $e) {
            Log::error('Checkout Session Verification Error: ' . $e->getMessage());
            return $this->responder->error(
                message: 'Failed to verify payment: ' . $e->getMessage(),
                code: 400
            );
        }
    }

    /**
     * Handle successful checkout session
     */
    private function handleCheckoutSessionCompleted($session): void
    {
        Log::info('Checkout session completed', ['session_id' => $session['id']]);
        $this->createSubscriptionFromSession($session);
    }

    /**
     * Handle successful payment intent
     */
    private function handlePaymentIntentSucceeded($paymentIntent): void
    {
        Log::info('Payment intent succeeded', ['payment_intent_id' => $paymentIntent['id']]);
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed($paymentIntent): void
    {
        Log::error('Payment intent failed', [
            'payment_intent_id' => $paymentIntent['id'],
            'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Unknown error'
        ]);
    }

    /**
     * Create subscription from Stripe session
     */
    private function createSubscriptionFromSession($session): Payment|Model
    {
        try {
            $metadata = $session['metadata']->toArray() ?? [];
            Log::debug($metadata);
            $userId = $metadata['user_id'] ?? null;

            if (!$userId) {
                Log::error('No user_id found in session metadata', ['session_id' => $session['id']]);
                throw new \InvalidArgumentException('No user_id found in session metadata');
            }

            // Check if payment already exists to avoid duplicates
            $existingPayment = Payment::query()
                ->where('reference', $session['id'])
                ->first();

            if ($existingPayment) {
                Log::info('Payment already exists for session', ['session_id' => $session['id']]);
                return $existingPayment;
            }

            $payment = PaymentRepository::new()->init(
                payerId: $userId,
                amount: $session['amount_total'], // This is already in pence/cents
                charges: 0,
                computedAmount: $session['amount_total'],
                ipAddress: '0.0.0.0', // Not available in webhook
                userAgent: 'Stripe Checkout',
                reference: $session['id'], // Use session ID as reference
                purpose: PaymentPurpose::PLAN_SUBSCRIPTION,
                metadata: $metadata,
            );

            Log::info('Subscription created successfully', [
                'user_id' => $userId,
                'session_id' => $session['id'],
                'amount' => $session['amount_total']
            ]);

            return $payment;
        } catch (\Exception $e) {
            Log::error('Failed to create subscription from session', [
                'session_id' => $session['id'],
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'payment_methods' => 'array',
            'payment_methods.*' => 'string'
        ]);

        $planId = SettingHelper::get(SystemSettingDefinition::ACTIVE_PLAN_ID);
        $plan = Plan::query()->findOrFail($planId);

        $defaultPaymentMethods = ['card', 'klarna', 'revolut_pay'];
        $paymentMethods = $request->payment_methods ?? $defaultPaymentMethods;

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $plan->price * 100,
                'currency' => 'gbp', // Changed to GBP
                'payment_method_types' => $paymentMethods,
                'metadata' => [
                    'user_id' => auth()->id(),
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name
                ]
            ]);

            return $this->responder->success(
                data: [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id
                ],
                message: ''
            );

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function confirmPayment(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method_id' => 'required|string'
        ]);

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            $paymentIntent->confirm([
                'payment_method' => $request->payment_method_id
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $this->createSubscription($paymentIntent);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment successful'
                ]);
            }

            return response()->json([
                'status' => $paymentIntent->status,
                'requires_action' => $paymentIntent->status === 'requires_action',
                'client_secret' => $paymentIntent->client_secret
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function createSubscription(PaymentIntent $paymentIntent): void
    {
        PaymentRepository::new()->init(
            payerId: Auth::id(),
            amount: $paymentIntent->amount,
            charges: 0,
            computedAmount: $paymentIntent->amount,
            ipAddress: \request()->ip(),
            userAgent: \request()->userAgent(),
            reference: PaymentHelper::generateLocalReference(),
            purpose: PaymentPurpose::PLAN_SUBSCRIPTION,
            metadata: $paymentIntent->metadata->toArray(),
        );
    }
}
