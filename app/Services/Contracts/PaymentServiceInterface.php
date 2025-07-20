<?php

namespace App\Services\Contracts;

use App\Dto\PaymentVerificationDto;
use App\Enums\Types\PaymentPurpose;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface PaymentServiceInterface
{
    public function initPayment(
        User|Model     $payer,
        float          $amount,
        string         $ipAddress,
        string         $userAgent,
        PaymentPurpose $purpose = PaymentPurpose::WALLET_FUNDING,
        ?string        $callbackUrlPrefix = null,
        array|object   $metadata = [],
    ): Payment|Model;


    public function verifyTransaction(Payment|Model $payment): PaymentVerificationDto;

    public function prepareInitialized(
        Payment|Model $payment,
        string        $localReference,
        string        $jsonResponse
    ): Model|Payment;
}
