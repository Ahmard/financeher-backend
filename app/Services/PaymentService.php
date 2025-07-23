<?php

namespace App\Services;

use App\Dto\PaymentVerificationDto;
use App\Enums\Statuses\PaymentStatus;
use App\Enums\SystemSettingDefinition;
use App\Enums\Types\PaymentGateway;
use App\Enums\Types\PaymentPurpose;
use App\Enums\Types\PaymentVerificationMethod;
use App\Exceptions\MaintenanceException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\PaymentHelper;
use App\Helpers\SettingHelper;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\PaymentRepository;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use PHPMailer\PHPMailer\Exception;
use Throwable;

class PaymentService extends BaseService
{
    public function __construct(
        public readonly PaymentRepository  $repository,
        protected readonly PaystackService $paystackService,
        protected readonly MonnifyService  $moniePointService,
        protected readonly SquadService    $squadService,
        protected readonly RemittaService  $remittaService,
        private readonly WalletService     $walletService,
    ) {
    }

    /**
     * @param User|Model|Authenticatable $payer
     * @param int|float $amount
     * @param string $ipAddress
     * @param string $userAgent
     * @param PaymentPurpose $purpose
     * @param string|null $callbackUrlPrefix
     * @param array|object $metadata
     * @return Payment|Model
     * @throws BindingResolutionException
     * @throws GuzzleException
     * @throws MaintenanceException
     * @throws Throwable
     */
    public function initPayment(
        User|Model|Authenticatable $payer,
        int|float                  $amount,
        string                     $ipAddress,
        string                     $userAgent,
        PaymentPurpose             $purpose = PaymentPurpose::WALLET_FUNDING,
        ?string                    $callbackUrlPrefix = null,
        array|object               $metadata = [],
    ): Payment|Model {
        SettingHelper::ensureModuleIsActive(SystemSettingDefinition::PAYMENT_MODULE_STATUS);

        return match (PaymentGateway::fromName(SettingHelper::get(SystemSettingDefinition::PAYMENT_GATEWAY))) {
            PaymentGateway::PAYSTACK => $this->paystackService->initPayment(
                payer: $payer,
                amount: $amount,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                purpose: $purpose,
                callbackUrlPrefix: $callbackUrlPrefix,
                metadata: $metadata,
            ),
            PaymentGateway::MONNIFY => $this->moniePointService->initPayment(
                payer: $payer,
                amount: $amount,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                purpose: $purpose,
                callbackUrlPrefix: $callbackUrlPrefix,
                metadata: $metadata,
            ),
            PaymentGateway::SQUAD => $this->squadService->initPayment(
                payer: $payer,
                amount: $amount,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                purpose: $purpose,
                callbackUrlPrefix: $callbackUrlPrefix,
                metadata: $metadata,
            ),
            PaymentGateway::REMITTA => $this->remittaService->initPayment(
                payer: $payer,
                amount: $amount,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                purpose: $purpose,
                callbackUrlPrefix: $callbackUrlPrefix,
                metadata: $metadata,
            ),
        };
    }

    /**
     * @param int $userId
     * @param string $reference
     * @return PaymentVerificationDto
     * @throws BindingResolutionException
     * @throws GuzzleException
     * @throws MaintenanceException
     * @throws ModelNotFoundException
     * @throws WarningException
     */
    #[ArrayShape([0 => 'bool', 1 => 'string', 2 => Payment::class])]
    public function verifyTransaction(int $userId, string $reference): PaymentVerificationDto
    {
        $payment = $this->repository->findByReference($reference);

        if (null == $payment) {
            throw new ModelNotFoundException('Such payment does not exists');
        }

        if (PaymentStatus::PAID->nameIs($payment['status'])) {
            throw new WarningException('Payment has been verified already!</br>Please refresh your page.');
        }

        SettingHelper::ensureModuleIsActive(SystemSettingDefinition::WALLET_MODULE_STATUS);

        $paymentGateway = PaymentGateway::fromName($payment['gateway']);

        $result = match ($paymentGateway) {
            PaymentGateway::PAYSTACK => $this->paystackService->verifyTransaction(payment: $payment),
            PaymentGateway::MONNIFY => $this->moniePointService->verifyTransaction(payment: $payment),
            PaymentGateway::SQUAD => $this->squadService->verifyTransaction(payment: $payment),
            PaymentGateway::REMITTA => $this->remittaService->verifyTransaction(payment: $payment),
        };

        DB::transaction(function () use ($payment, $result, $userId, $paymentGateway) {
            $payload = json_decode(
                json: $result->jsonResponse,
                associative: true
            );

            if (!empty($payload) && $result->isPaid) {
                $this->markPaymentAsPaid(
                    userId: $userId,
                    payload: $payload,
                    payment: $payment,
                    paymentGateway: $paymentGateway,
                    verificationMethod: PaymentVerificationMethod::POLLING
                );
            }

            if ($result->status == PaymentStatus::EXPIRED) {
                $this->markAsExpired($payment);
            }
        });

        return $result;
    }

    /**
     * @param int $userId
     * @param array $payload
     * @param Payment|Model $payment
     * @param PaymentGateway $paymentGateway
     * @param PaymentVerificationMethod $verificationMethod
     * @return Model|Payment
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function markPaymentAsPaid(
        int                       $userId,
        array                     $payload,
        Payment|Model             $payment,
        PaymentGateway            $paymentGateway,
        PaymentVerificationMethod $verificationMethod,
    ): Model|Payment {
        $responsePaidAmount = match ($paymentGateway) {
            // MONIEPOINT
            PaymentGateway::MONNIFY => $verificationMethod === PaymentVerificationMethod::POLLING
                ? $payload['responseBody']['amountPaid']
                : $payload['eventData']['amountPaid'],

            // PAYSTACK
            PaymentGateway::PAYSTACK => $payload['data']['amount'],

            // SQUAD
            PaymentGateway::SQUAD => (function () use ($payload, $verificationMethod) {
                if ($verificationMethod == PaymentVerificationMethod::WEBHOOK) {
                    return $payload['Body']['amount'];
                }

                return $payload['data']['transaction_amount'];
            })(),

            // REMITTA
            PaymentGateway::REMITTA => $payload['amount'],
        };

        $payment->update([
            'paid_at' => date('Y-m-d H:i:s'),
            'paid_amount' => $responsePaidAmount,
            'webhook_event' => json_encode($payload),
            'status' => PaymentStatus::PAID->lowercase(),
            'verification_method' => $verificationMethod->lowercase(),
        ]);

        // Handle payment purpose
        $this->handlePaidMoneyPurpose($payment);

        return $payment;
    }

    public function markAsExpired(Payment|Model $payment): void
    {
        $payment->update([
            'status' => PaymentStatus::EXPIRED->lowercase(),
        ]);

    }

    /**
     * @param User|Model $payer
     * @param string $reference
     * @param string $localReference
     * @param float $amount
     * @param string $ipAddress
     * @param string $userAgent
     * @param PaymentPurpose $purpose
     * @param PaymentStatus $status
     * @param PaymentGateway $paymentGateway
     * @param string|null $initResponse
     * @param array $additionalData
     * @param string|null $webhookEvent
     * @param bool $isVAN Specifies this is virtual account number transfer
     * @param bool $isCard
     * @param bool $isDirectTransfer
     * @return Model|Payment
     */
    public function createPaymentRecord(
        User|Model     $payer,
        string         $reference,
        string         $localReference,
        float          $amount,
        string         $ipAddress,
        string         $userAgent,
        PaymentPurpose $purpose,
        PaymentStatus  $status,
        PaymentGateway $paymentGateway,
        ?string        $initResponse,
        array          $additionalData = [],
        ?string        $webhookEvent = null,
        bool           $isVAN = false,
        bool           $isCard = false,
        bool           $isDirectTransfer = false,
    ): Model|Payment {
        $charges = PaymentHelper::calculateCharges(
            amount: $amount,
            paymentGateway: $paymentGateway,
            isVAN: $isVAN,
            isCard: $isCard,
        );

        $payment = $this->repository->create([
            'payer_id' => $payer['id'],
            'reference' => $reference,
            'local_reference' => $localReference,
            'amount' => $amount,
            'paid_at' => date('Y-m-d H:i:s'),
            'paid_amount' => $amount,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'init_response' => $initResponse,
            'webhook_event' => $webhookEvent,
            'charges' => $charges['charges'],
            'status' => $status->lowercase(),
            'purpose' => $purpose->lowercase(),
            'is_direct_transfer' => $isDirectTransfer,
            'computed_amount' => $charges['computed_amount'],
            'gateway' => $paymentGateway->lowercase(),
            'additional_data' => json_encode($additionalData),
        ]);

        $this->handlePaidMoneyPurpose($payment);

        return $payment;
    }

    /**
     * @param Payment|Model $payment
     * @return void
     */
    private function handlePaidMoneyPurpose(Payment|Model $payment): void
    {
        // Unit Top-Up
        if (PaymentPurpose::WALLET_FUNDING->nameIs($payment['purpose'])) {
            $this->walletService->credit(
                userId: $payment['payer_id'],
                amount: $payment['paid_amount'],
                narration: "Wallet Funding",
            );
        }
    }
}
