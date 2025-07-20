<?php

namespace App\Services;

use App\Dto\PaymentVerificationDto;
use App\Enums\Statuses\PaymentStatus;
use App\Enums\Types\PaymentGateway;
use App\Enums\Types\PaymentPurpose;
use App\Helpers\PaymentHelper;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\PaymentRepository;
use App\Services\Contracts\PaymentServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;

class PaystackService extends BaseService implements PaymentServiceInterface
{
    public function __construct(
        protected readonly PaymentRepository $paymentRepository,
    )
    {
    }

    /**
     * @param User|Model $payer
     * @param float $amount
     * @param string $ipAddress
     * @param string $userAgent
     * @param PaymentPurpose $purpose
     * @param string|null $callbackUrlPrefix
     * @param array|object $metadata
     * @return Payment|Model
     * @throws GuzzleException
     * @throws Throwable
     */
    public function initPayment(
        User|Model     $payer,
        float          $amount,
        string         $ipAddress,
        string         $userAgent,
        PaymentPurpose $purpose = PaymentPurpose::WALLET_FUNDING,
        ?string        $callbackUrlPrefix = null,
        array|object   $metadata = [],
    ): Payment|Model
    {
        try {
            $computed = PaymentHelper::calculateCharges(
                amount: $amount,
                paymentGateway: PaymentGateway::PAYSTACK
            );

            $payment = $this->paymentRepository->init(
                payerId: $payer['id'],
                amount: $amount,
                charges: $computed['charges'],
                computedAmount: $computed['computed_amount'],
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                purpose: $purpose,
                metadata: $metadata,
                paymentGateway: PaymentGateway::PAYSTACK,
            );

            $localReference = PaymentHelper::generateLocalReference();

            if (defined('IS_MIGRATING')) {  // Migrating
                $reference = uniqid(prefix: 'fake-');
                $jsonResponse = '{"data": {"reference": "' . $reference . '", "access_code": "n5jg6fo4fb4cqp9", "authorization_url": "https://checkout.paystack.com/n5jg6fo4fb4cqp9"}, "status": true, "message": "Authorization URL created"}';
            } else {    // Live Site
                $response = (new Client())->post(
                    uri: config('payment.paystack.endpoint') . 'transaction/initialize',
                    options: [
                        'headers' => [...$this->getBearerToken()],
                        'json' => [
                            'ref' => $localReference,
                            'email' => $payer['email'],
                            'amount' => $amount,
                            'callback_url' => config('app.frontend_address')
                                . ($callbackUrlPrefix ?? "wallet/funding-history/{$payment['id']}")
                        ]
                    ]
                );

                $jsonResponse = $response->getBody()->getContents();
            }

            return $this->prepareInitialized(
                payment: $payment,
                localReference: $localReference,
                jsonResponse: $jsonResponse
            );
        } catch (Throwable $e) {
            if (isset($payment)) {
                $payment->forceDelete();
            }

            Log::debug($e);

            throw $e;
        }
    }

    private function getBearerToken(): array
    {
        return ['Authorization' => 'Bearer ' . config('payment.paystack.secret-key')];
    }

    /**
     * @param Payment|Model $payment
     * @return PaymentVerificationDto
     * @throws GuzzleException
     */
    public function verifyTransaction(Payment|Model $payment): PaymentVerificationDto
    {
        $response = (new Client())->get(
            uri: config('payment.paystack.endpoint') . 'transaction/verify/' . $payment['reference'],
            options: [
                'headers' => [...self::getBearerToken()],
            ]
        );

        $jsonResponse = $response->getBody()->getContents();
        $payload = json_decode(
            json: $jsonResponse,
            associative: true
        );

        $isPaid = false;
        $status = PaymentStatus::PENDING;

        if (!empty($payload)) {
            $isPaid = 'success' == $payload['data']['status'];
            $status = match ($payload['responseBody']['paymentStatus']) {
                "success" => PaymentStatus::PAID,
                "expired" => PaymentStatus::EXPIRED,
                default => PaymentStatus::PENDING,
            };
        }

        return new PaymentVerificationDto(
            isPaid: $isPaid,
            jsonResponse: $jsonResponse,
            payment: $payment,
            status: $status,
            gateway: PaymentGateway::MONNIFY
        );
    }

    public function prepareInitialized(Model|Payment $payment, string $localReference, string $jsonResponse): Model|Payment
    {
        $payload = json_decode($jsonResponse, true);
        $payment->update([
            'init_response' => $jsonResponse,
            'local_reference' => $localReference,
            'reference' => $payload['data']['reference'],
            'payment_url' => $payload['data']['authorization_url'],
        ]);
        return $payment;
    }
}
