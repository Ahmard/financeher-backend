<?php

namespace App\Enums\EnumHelper;

interface DBCompatibleEnumInterface
{
    public static function has(string $key): bool;

    public static function fromName(string $name): static;

    public static function casesExcept(array $cases): array;

    public static function getDBCompatibleEnum(bool $keyAsLowercase = true): array;

    public function lowercase(): string;
}
