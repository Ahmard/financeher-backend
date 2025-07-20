<?php

namespace App\Enums\EnumHelper;

use InvalidArgumentException;

trait DBCompatibleEnumTrait
{
    public static function count(): int
    {
        return count(static::cases()) - 1;
    }

    public static function has(string $key): bool
    {
        return in_array(
            needle: strtolower($key),
            haystack: static::getDBCompatibleEnum()
        );
    }

    public static function getDBCompatibleEnum(bool $keyAsLowercase = true): array
    {
        return array_map(
            callback: fn ($status) => $keyAsLowercase
                ? $status->lowercase()
                : $status->name,
            array: static::cases()
        );
    }

    public function lowercase(): string
    {
        return strtolower($this->name);
    }

    /**
     * @param array $cases
     * @return static[]
     */
    public static function casesExcept(array $cases): array
    {
        $cases = self::toDBUsable($cases);
        $enumCases = static::cases();
        $retCases = [];

        foreach ($enumCases as $enumCase) {
            if (in_array($enumCase->lowercase(), $cases)) {
                continue;
            }

            $retCases[] = $enumCase;
        }

        return $retCases;
    }

    protected static function toDBUsable(array $cases, bool $keyAsLowercase = true): array
    {
        return array_map(
            callback: fn ($status) => $keyAsLowercase
                ? $status->lowercase()
                : $status->name,
            array: $cases
        );
    }

    public static function fromName(string $name): static
    {
        $name = strtoupper($name);
        foreach (static::cases() as $case) {
            if ($case->name == $name) {
                return $case;
            }
        }

        $className = get_class();
        throw new InvalidArgumentException("Case '$name' not found in '$className' enum");
    }

    public static function random(): static
    {
        $values = static::cases();
        return $values[array_rand($values)];
    }

    public static function randomValue(): string
    {
        $values = static::getDBCompatibleEnum();
        return $values[array_rand($values)];
    }

    /**
     * @param DBCompatibleEnumInterface $enum
     * @return bool
     */
    public function equals(DBCompatibleEnumInterface $enum): bool
    {
        return $enum == $this;
    }

    public function notEquals(DBCompatibleEnumInterface $enum): bool
    {
        return !$this->equals($enum);
    }

    /**
     * @param string|null $name
     * @return bool
     */
    public function valueIs(?string $name): bool
    {
        return strtolower($this->value) == strtolower($name ?? '');
    }

    /**
     * @param string|null $name
     * @return bool
     */
    public function valueIsNot(?string $name): bool
    {
        return !$this->valueIs($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function nameIsNot(string $name): bool
    {
        return !$this->nameIs($name);
    }

    /**
     * Check if name matched given value
     *
     * @param string $name
     * @return bool
     */
    public function nameIs(string $name): bool
    {
        return $this->lowercase() == strtolower($name);
    }
}
