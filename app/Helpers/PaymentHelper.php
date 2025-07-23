<?php

namespace App\Helpers;

use App\Enums\SystemSettingDefinition;
use App\Enums\Types\PaymentGateway;
use JetBrains\PhpStorm\ArrayShape;

class PaymentHelper
{
    private const PAYSTACK_AMOUNT_WITHOUT_CHARGES = 2500;
    private const PAYSTACK_CHARGES_PERCENTAGE = 1.5;
    private const PAYSTACK_ADDITIONAL_CHARGES = 100;

    private const SQUAD_CHARGES_PERCENTAGE = 0.25;
    private const REMITTA_CHARGES_PERCENTAGE = 0.25;

    #[ArrayShape(['charges' => 'float', 'computed_amount' => 'float', 'amount' => 'float'])]
    public static function calculateCharges(
        float          $amount,
        PaymentGateway $paymentGateway,
        bool           $isVAN = false,
        bool           $isCard = false
    ): array {
        return match ($paymentGateway) {
            PaymentGateway::PAYSTACK => self::paystack($amount),
            PaymentGateway::MONNIFY => self::moniepoint($amount, $isVAN, $isCard),
            PaymentGateway::SQUAD => self::squad($amount),
            PaymentGateway::REMITTA => self::remitta($amount),
        };
    }

    public static function isHashValid(string $signature, string $input, PaymentGateway $paymentGateway): bool
    {
        return match ($paymentGateway) {
            PaymentGateway::PAYSTACK => self::computePaystackHash($signature, $input),
            PaymentGateway::MONNIFY => self::computeMoniepointHash($signature, $input),
            PaymentGateway::SQUAD => self::computeSquadHash($signature, $input),
            PaymentGateway::REMITTA => self::computeRemittaHash($signature, $input),
        };
    }

    public static function generateLocalReference(): string
    {
        $random_number = mt_rand(100000, 999999);
        $timestamp = round(microtime(true) * 1000);

        return sprintf('TLR|PAYMENT|%d|%s', $timestamp, $random_number);
    }

    private static function computePaystackHash(string $signature, string $input): bool
    {
        return $signature === hash_hmac('sha512', $input, config('payment.paystack.secret-key'));
    }

    private static function computeMoniepointHash(string $signature, string $input): bool
    {
        return $signature === hash_hmac('sha512', $input, config('payment.moniepoint.secret-key'));
    }

    private static function computeSquadHash(string $signature, string $input): bool
    {
        return $signature === hash_hmac('sha512', $input, config('payment.squad.secret-key'));
    }

    private static function computeRemittaHash(string $signature, string $input): bool
    {
        return $signature === hash_hmac('sha512', $input, config('payment.remitta.secret-key'));
    }

    private static function paystack(float $amount): array
    {
        $charges = ($amount / 100) * self::PAYSTACK_CHARGES_PERCENTAGE;

        if ($amount >= self::PAYSTACK_AMOUNT_WITHOUT_CHARGES) {
            $charges = (($amount / 100) * self::PAYSTACK_CHARGES_PERCENTAGE) + self::PAYSTACK_ADDITIONAL_CHARGES;
        }

        return [
            'amount' => $amount,
            'charges' => $charges,
            'computed_amount' => $amount - $charges,
        ];
    }

    private static function moniepoint(float $amount, bool $isVAN = false, bool $isCard = false): array
    {
        $mpVat = SettingHelper::getFloat(SystemSettingDefinition::MONIEPOINT_VAT);
        $mpCharges = $isCard
            ? SettingHelper::getFloat(SystemSettingDefinition::MONIEPOINT_CARD_CHARGES)
            : (
                $isVAN
                ? SettingHelper::getFloat(SystemSettingDefinition::MONIEPOINT_VAN_TRANSFER_CHARGES)
                : SettingHelper::getFloat(SystemSettingDefinition::MONIEPOINT_TRANSFER_CHARGES)
            );

        $price = $amount * ($mpCharges / 100);
        $vat = $price * $mpVat;
        $charges = $price + $vat;

        return [
            'amount' => $amount,
            'charges' => $charges,
            'computed_amount' => $amount - $charges,
        ];
    }

    #[ArrayShape(['charges' => 'float', 'computed_amount' => 'float', 'amount' => 'float'])]
    private static function squad(float $amount): array
    {
        $charges = ($amount / 100) * self::SQUAD_CHARGES_PERCENTAGE;

        return [
            'amount' => $amount,
            'charges' => $charges,
            'computed_amount' => $amount - $charges,
        ];
    }

    #[ArrayShape(['charges' => 'float', 'computed_amount' => 'float', 'amount' => 'float'])]
    private static function remitta(float $amount): array
    {
        $charges = ($amount / 100) * self::REMITTA_CHARGES_PERCENTAGE;

        return [
            'amount' => $amount,
            'charges' => $charges,
            'computed_amount' => $amount - $charges,
        ];
    }
}
