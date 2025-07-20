<?php

namespace App\Dto;

use App\Enums\Statuses\PaymentStatus;
use App\Enums\Types\PaymentGateway;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;

readonly class PaymentVerificationDto
{
    public function __construct(
        public bool           $isPaid,
        public string         $jsonResponse,
        public Payment|Model  $payment,
        public PaymentStatus  $status,
        public PaymentGateway $gateway,
    )
    {
    }
}