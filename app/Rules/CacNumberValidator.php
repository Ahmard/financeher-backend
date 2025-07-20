<?php

namespace App\Rules;

class CacNumberValidator extends BaseValidator
{
    public function __construct()
    {
    }

    /**
     * @return CacNumberValidator
     */
    public static function create(): CacNumberValidator
    {
        return new CacNumberValidator();
    }

    public function __invoke($attribute, $value, $fail): void
    {
        // Check if the value is empty
        if (empty($value)) {
            $fail("The $attribute field is required.");
            return;
        }

        // Ensure the cac number is in correct syntax
        $prefix = substr($value, 0, 3); // Get the first three characters of the string
        $hasValidPrefix = $prefix === 'RC-' || $prefix === 'BN-' || $prefix === 'IT-';
        if ($hasValidPrefix) {
            return;
        }

        $fail("The $attribute must be in the format 'RC-XXXXXXXXXX' or 'BN-XXXXXXXXXX' or 'IT-XXXXXXXXXX'.");
    }
}
