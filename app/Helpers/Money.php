<?php

namespace App\Helpers;

class Money
{
    public static function formatCent(string|int|float $amount): string
    {
        return self::format(self::fromCent($amount));
    }

    public static function format(string|int|float $amount): string
    {
        return number_format($amount, 2);
    }

    public static function fromCent(int $amount): float
    {
        return $amount / 100;
    }

    public static function removeCommas(string $formattedNumber, bool $returnFloat = false): int|float
    {
        $commaStripped = str_replace(',', '', $formattedNumber);
        return $returnFloat ? floatval($commaStripped) : intval($commaStripped);
    }

    public static function toCent(int|float $amount): int
    {
        return $amount * 100;
    }
}
