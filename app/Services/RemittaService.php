<?php

namespace App\Services;

use App\Dto\PaymentVerificationDto;
use App\Enums\Statuses\PaymentStatus;
use App\Enums\SystemSettingDefinition;
use App\Enums\Types\PaymentGateway;
use App\Enums\Types\PaymentPurpose;
use App\Exceptions\ConfigItemNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\PaymentHelper;
use App\Helpers\SettingHelper;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\PaymentRepository;
use App\Services\Contracts\PaymentServiceInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use stdClass;
use Throwable;

class RemittaService extends BaseService implements PaymentServiceInterface
{
    private string $generateTokenPath = 'remita/exapp/api/v1/send/api/uaasvc/uaa/token';
    private string $rrrGenerationPath = 'remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit';

    public function initPayment(
        Model|User     $payer,
        float          $amount,
        string         $ipAddress,
        string         $userAgent,
        PaymentPurpose $purpose = PaymentPurpose::WALLET_FUNDING,
        ?string        $callbackUrlPrefix = null,
        object|array   $metadata = [],
    ): Payment|Model {
        ['orderId' => $orderId, 'hash' => $apiHash] = $this->makeNewTransactionData($amount);

        // Prepare the request body
        $requestBody = [
            'serviceTypeId' => config('payment.remitta.service-type-id'),
            'amount' => $amount,
            'orderId' => $orderId,
            'payerName' => $payer->fullName(),
            'payerEmail' => $payer['email'],
            'payerPhone' => $payer['mobile_number'],
            'description' => 'Payment for ' . $purpose->lowercase(),
        ];

        // Initialize Guzzle client
        $client = new Client();

        try {
            $computed = PaymentHelper::calculateCharges(
                amount: $amount,
                paymentGateway: PaymentGateway::REMITTA
            );

            $payment = PaymentRepository::new()->init(
                payerId: $payer['id'],
                amount: $amount,
                charges: $computed['charges'],
                computedAmount: $computed['computed_amount'],
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                reference: $orderId,
                purpose: $purpose,
                metadata: $metadata,
                paymentGateway: PaymentGateway::REMITTA
            );

            // Send the request to Remitta
            $endpoint = config('payment.remitta.endpoint') . $this->rrrGenerationPath;
            $response = $client->post($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $this->makeAuthHeaderValue($apiHash),
                ],
                'json' => $requestBody,
            ]);

            $jsonResponse = $response->getBody()->getContents();

            // Handle JSONP response
            if (str_starts_with($jsonResponse, 'jsonp (')) {
                // Extract JSON from JSONP
                $jsonResponse = trim($jsonResponse, 'jsonp ()');
            }

            return $this->prepareInitialized(
                payment: $payment,
                localReference: $orderId,
                jsonResponse: $jsonResponse
            );
        } catch (RequestException $e) {
            Log::error($e);
            // Handle error
            throw new Exception('Failed to initialize payment: ' . $e->getResponse()->getBody()->getContents());
        } catch (Exception $e) {
            Log::error($e);
            // Handle error
            throw new Exception('Failed to initialize payment: ' . $e->getMessage());
        }
    }

    /**
     * @param Model|Payment $payment
     * @return PaymentVerificationDto
     * @throws WarningException
     */
    public function verifyTransaction(Model|Payment $payment): PaymentVerificationDto
    {
        $apiAuthHash = $this->generateRrrVerificationHash($payment['reference']);
        $path = sprintf(
            'remita/exapp/api/v1/send/api/echannelsvc/%s/%s/%s/status.reg',
            config('payment.remitta.merchant-id'),
            $payment['reference'],
            $apiAuthHash,
        );

        try {
            $response = (new Client())->get(
                uri: config('payment.remitta.endpoint') . $path,
                options: [
                    'headers' => ['Content-Type' => 'application/json'],
                ]
            );

            $jsonResponse = $response->getBody()->getContents();

            $payload = json_decode(
                json: $jsonResponse,
                associative: true
            );

            $status = PaymentStatus::PENDING;

            if (!empty($payload) && $payload['status']) {
                $status = match ($payload['status']) {
                    '00' => PaymentStatus::PAID,
                    '063' => PaymentStatus::EXPIRED,
                    default => PaymentStatus::PENDING,
                };
            }

            return new PaymentVerificationDto(
                isPaid: $status == PaymentStatus::PAID,
                jsonResponse: $jsonResponse,
                payment: $payment,
                status: $status,
                gateway: PaymentGateway::REMITTA
            );
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new WarningException('Payment verification failed');
        }
    }

    public function prepareInitialized(Model|Payment $payment, string $localReference, string $jsonResponse): Model|Payment
    {
        $payload = json_decode($jsonResponse, true);
        $payment->update([
            'init_response' => $jsonResponse,
            'local_reference' => $localReference,
            'reference' => $payload['RRR'],
            'payment_url' => null
        ]);
        return $payment;
    }

    /**
     * @param float $amount
     * @return array{hash: string, orderId: string}
     */
    private function makeNewTransactionData(float $amount): array
    {
        $orderId = PaymentHelper::generateLocalReference();
        return [
            'orderId' => $orderId,
            'hash' => $this->generateRrrCreationHash($orderId, $amount),
        ];
    }

    /**
     * @param string $orderId
     * @param float $amount
     * @return string
     */
    private function generateRrrCreationHash(string $orderId, float $amount): string
    {
        $serviceTypeId = config('payment.remitta.service-type-id');
        $merchantId = config('payment.remitta.merchant-id');
        $apiKey = config('payment.remitta.api-key');
        $apiHashData = $merchantId . $serviceTypeId . $orderId . $amount . $apiKey;

        return hash('sha512', $apiHashData);
    }

    /**
     * @param string $rrr
     * @return string
     */
    private function generateRrrVerificationHash(string $rrr): string
    {
        $merchantId = config('payment.remitta.merchant-id');
        $apiKey = config('payment.remitta.api-key');
        $apiHashData = $rrr . $apiKey . $merchantId;

        return hash('sha512', $apiHashData);
    }

    /**
     * @return string
     * @throws BindingResolutionException
     * @throws GuzzleException
     * @phpstan-ignore-next-line
     */
    private function getBearerToken(): string
    {
        $authData = null;
        $authToken = null;

        try {
            $authData = SettingHelper::get(SystemSettingDefinition::REMITTA_AUTH_TOKEN);
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
                definition: SystemSettingDefinition::REMITTA_AUTH_TOKEN,
                value: json_encode($authData)
            );
        }

        return $authToken;
    }

    /**
     * @return stdClass
     * @throws GuzzleException
     */
    private function acquireAuthToken(): stdClass
    {
        $response = (new Client())->post(
            uri: config('payment.remitta.endpoint') . $this->generateTokenPath,
            options: [
                'json' => [
                    'username' => config('payment.remitta.public-key'),
                    'password' => config('payment.remitta.secret-key'),
                ],
            ]
        );

        $jsonResponse = $response->getBody()->getContents();
        $payload = json_decode(json: $jsonResponse, associative: true) ?? [];

        return $payload['data'][0];
    }

    private function makeAuthHeaderValue(string $hash): string
    {
        return 'remitaConsumerKey=' . config('payment.remitta.merchant-id') . ',remitaConsumerToken=' . $hash;
    }
}
