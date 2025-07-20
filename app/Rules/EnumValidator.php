<?php

namespace App\Rules;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;

class EnumValidator extends BaseValidator
{
    public function __construct(protected string|DBCompatibleEnumInterface $enum)
    {
    }

    /**
     * @param class-string|DBCompatibleEnumInterface $enum
     * @return EnumValidator
     */
    public static function create(string|DBCompatibleEnumInterface $enum): EnumValidator
    {
        return new EnumValidator($enum);
    }

    public function __invoke($attribute, $value, $fail): void
    {
        if (empty($value) || !$this->enum::has($value)) {
            $fail("Please provide valid '$attribute' type");
        }
    }
}
