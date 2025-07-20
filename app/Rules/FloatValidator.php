<?php

namespace App\Rules;

class FloatValidator extends BaseValidator
{
    public function __construct(protected bool $canBeInt = false)
    {
    }

    public static function create(bool $canBeInt = false): FloatValidator
    {
        return new FloatValidator($canBeInt);
    }

    public function __invoke($attribute, $value, $fail): void
    {
        if (empty($value) || !is_float($value)) {
            $fail("Please provide valid '$attribute' value");
        }
    }
}
