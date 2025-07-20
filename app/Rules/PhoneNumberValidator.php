<?php

namespace App\Rules;

class PhoneNumberValidator extends BaseValidator
{
    public function __construct()
    {
    }

    /**
     * @return PhoneNumberValidator
     */
    public static function create(): PhoneNumberValidator
    {
        return new PhoneNumberValidator();
    }

    public function __invoke($attribute, $value, $fail): void
    {
        // Check if the value is empty
        if (empty($value)) {
            $fail("The $attribute field is required.");
            return;
        }

        // Ensure the phone number starts with specific prefixes and has exactly 11 digits
        if (!preg_match('/^(080|081|090|091|070)\d{8}$/', $value)) {
            $fail("The $attribute must be a valid mobile number starting with 080, 081, 090, 091, or 070 and be 11 digits long.");
            return;
        }
    }
}
