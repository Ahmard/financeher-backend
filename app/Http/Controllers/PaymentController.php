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
use App\Models\Plan;
use App\Repositories\PaymentRepository;
use App\Services\PaymentService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Stripe\PaymentIntent;
use Stripe\Stripe;

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


    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'payment_methods' => 'array', // Accept multiple methods
            'payment_methods.*' => 'string'
        ]);

        $planId = SettingHelper::get(SystemSettingDefinition::ACTIVE_PLAN_ID);
        $plan = Plan::query()->findOrFail($planId);

        // Default payment methods for UK
        $defaultPaymentMethods = ['card', 'klarna', 'revolut_pay'];

        $paymentMethods = $request->payment_methods ?? $defaultPaymentMethods;

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $plan->price * 100, // Stripe uses cents
                'currency' => 'usd',
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
                // Create subscription record
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
