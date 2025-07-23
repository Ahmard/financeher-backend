<?php

namespace App\Services;

use App\Dto\PaymentVerificationDto;
use App\Enums\Statuses\PaymentStatus;
use App\Enums\SystemSettingDefinition;
use App\Enums\Types\PaymentGateway;
use App\Enums\Types\PaymentPurpose;
use App\Exceptions\ConfigItemNotFoundException;
use App\Helpers\PaymentHelper;
use App\Helpers\SettingHelper;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\PaymentRepository;
use App\Services\Contracts\PaymentServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use stdClass;
use Throwable;

class MonnifyService extends BaseService implements PaymentServiceInterface
{
    public function __construct(
        protected readonly PaymentRepository $paymentRepository,
    ) {
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
     * @throws BindingResolutionException
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
    ): Payment|Model {
        $precisedAmount = round(num: $amount, precision: 2);

        try {
            $computed = PaymentHelper::calculateCharges(
                amount: $amount,
                paymentGateway: PaymentGateway::MONNIFY
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
                $jsonResponse = '{"requestSuccessful":true,"responseMessage":"success","responseCode":"0","responseBody":{"transactionReference":"' . $reference . '","paymentReference":"' . $localReference . '","merchantName":"NinAuth","apiKey":"MK_TEST_B4PU4H0PKK","redirectUrl":"https://ninauth.com/payments/2","enabledPaymentMethod":["CARD","ACCOUNT_TRANSFER"],"checkoutUrl":"https://sandbox.sdk.monnify.com/checkout/' . $reference . '"}}';
            } else {    // Live Site
                $response = (new Client())->post(
                    uri: config('payment.moniepoint.endpoint') . 'api/v1/merchant/transactions/init-transaction',
                    options: [
                        'headers' => [...$this->getBearerToken()],
                        'json' => [
                            'amount' => $precisedAmount,
                            'customerName' => $payer->fullName(),
                            'customerEmail' => $payer['email'],
                            'currencyCode' => 'NGN',
                            'paymentReference' => $localReference,
                            'contractCode' => config('payment.moniepoint.contract-code'),
                            'paymentMethods' => ['CARD', 'ACCOUNT_TRANSFER'],
                            'paymentDescription' => $purpose->lowercase(),
                            'redirectUrl' => frontend($callbackUrlPrefix ?? "payments/{$payment['id']}")
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

    /**
     * @return string[]
     * @throws BindingResolutionException
     * @throws GuzzleException
     */
    private function getBearerToken(): array
    {
        $authData = null;
        $authToken = null;

        try {
            $authData = SettingHelper::get(SystemSettingDefinition::MONIEPOINT_AUTH_TOKEN);
            $authData = json_decode($authData);
            if (property_exists($authData, 'accessToken')) {
                $authToken = $authData?->accessToken;
            } else {
                $authData = null;
            }
        } catch (ConfigItemNotFoundException) {
        }

        if (empty($authData) || time() > $authData->expiresIn) {
            $authData = $this->acquireAuthToken();
            $authToken = $authData->accessToken;

            $authData->expiresIn = time() + $authData->expiresIn;
            SystemSettingService::new()->updateItem(
                definition: SystemSettingDefinition::MONIEPOINT_AUTH_TOKEN,
                value: json_encode($authData)
            );
        }

        return ['Authorization' => 'Bearer ' . $authToken];
    }

    /**
     * @return stdClass
     * @throws GuzzleException
     */
    private function acquireAuthToken(): stdClass
    {
        $apiKey = config('payment.moniepoint.api-key');
        $secretKey = config('payment.moniepoint.secret-key');

        $response = (new Client())->post(
            uri: config('payment.moniepoint.endpoint') . 'api/v1/auth/login',
            options: [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(string: "$apiKey:$secretKey"),
                ],
            ]
        );

        $jsonResponse = $response->getBody()->getContents();
        $payload = json_decode(json: $jsonResponse);

        return $payload->responseBody;
    }

    /**
     * @param Model|Payment $payment
     * @return PaymentVerificationDto
     * @throws BindingResolutionException
     * @throws GuzzleException
     */
    public function verifyTransaction(Model|Payment $payment): PaymentVerificationDto
    {
        $response = (new Client())->get(
            uri: config('payment.moniepoint.endpoint') . 'api/v2/transactions/' . $payment['gateway_reference'],
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

        if (!empty($payload) && $payload['requestSuccessful']) {
            $isPaid = 'PAID' == $payload['responseBody']['paymentStatus'];
            $status = match ($payload['responseBody']['paymentStatus']) {
                "PAID" => PaymentStatus::PAID,
                "EXPIRED" => PaymentStatus::EXPIRED,
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
            'reference' => $localReference,
            'gateway_reference' => $payload['responseBody']['transactionReference'],
            'checkout_url' => $payload['responseBody']['checkoutUrl']
        ]);
        return $payment;
    }
}
