<?php

namespace App\Services;

use App\Helpers\Money;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripeService extends BaseService
{
    /**
     * @param User|Model|Authenticatable $payer
     * @param int $amount
     * @param array $metadata
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function createIntent(
        User|Model|Authenticatable $payer,
        int                        $amount,
        array                      $metadata
    ): PaymentIntent
    {
        $stripe = new StripeClient(config('payment.stripe.secret-key'));

        return $stripe->paymentIntents->create([
            'amount' => Money::toCent($amount),
            'currency' => 'usd',
            'metadata' => $metadata,
            'receipt_email' => $payer['email'],
            'payment_method_types' => ['card'],
        ]);
    }

    /**
     * @param Model|Payment $payment
     * @return array{0: bool, 1: PaymentIntent, 2: Payment|Model}
     * @throws ApiErrorException
     */
    public function verifyTransaction(Model|Payment $payment): array
    {
        $stripe = new StripeClient(config('payment.stripe.secret-key'));
        $intent = $stripe->paymentIntents->retrieve($payment['reference']);
        $status = $intent->status == 'succeeded';

        return [$status, $intent, $payment];
    }
}
