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
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

class SquadService extends BaseService implements PaymentServiceInterface
{
    public function __construct(
        protected readonly PaymentRepository $paymentRepository,
    ) {
    }

    public function initPayment(
        Model|User     $payer,
        float          $amount,
        string         $ipAddress,
        string         $userAgent,
        PaymentPurpose $purpose = PaymentPurpose::WALLET_FUNDING,
        ?string        $callbackUrlPrefix = null,
        object|array $metadata = []
    ): Payment|Model {
        $computed = PaymentHelper::calculateCharges(
            amount: $amount,
            paymentGateway: PaymentGateway::SQUAD
        );

        $localReference = PaymentHelper::generateLocalReference();

        $payment = PaymentRepository::new()->init(
            payerId: $payer['id'],
            amount: $amount,
            charges: $computed['charges'],
            computedAmount: $computed['computed_amount'],
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            reference: $localReference,
            purpose: $purpose,
            metadata: $metadata,
        );

        if (defined('IS_MIGRATING')) {  // Migrating
            $reference = uniqid(prefix: 'fake-');
            $jsonResponse = '{"data": {"reference": "' . $reference . '", "access_code": "n5jg6fo4fb4cqp9", "authorization_url": "https://checkout.paystack.com/n5jg6fo4fb4cqp9"}, "status": true, "message": "Authorization URL created"}';
        } else {    // Live Site
            $response = (new Client())->post(
                uri: config('payment.squad.endpoint') . 'transaction/initiate',
                options: [
                    'headers' => [...$this->getBearerToken()],
                    'json' => [
                        'email' => $payer['email'],
                        'amount' => $amount,
                        'initiate_type' => 'inline',
                        'currency' => 'NGN',
                        'transaction_ref' => $localReference,
                        'customer_name' => $payer->fullName(),
                        'channels' => ['transfer', 'card', 'ussd'],
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
    }

    private function getBearerToken(): array
    {
        return ['Authorization' => config('payment.squad.secret-key')];
    }

    public function verifyTransaction(Model|Payment $payment): PaymentVerificationDto
    {
        $response = (new Client())->get(
            uri: config('payment.squad.endpoint') . 'transaction/verify/' . $payment['reference'],
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
            $isPaid = 'success' == $payload['data']['transaction_status'];
            $status = match ($payload['data']['transaction_status']) {
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
            'reference' => $payload['data']['transaction_ref'],
            'checkout_url' => $payload['data']['checkout_url']
        ]);
        return $payment;
    }
}
