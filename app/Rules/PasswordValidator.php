<?php

namespace App\Rules;

class PasswordValidator extends BaseValidator
{
    /**
     * Constructor is not needed for this specific validator, but can be extended if necessary.
     */
    public function __construct()
    {
    }

    /**
     * Factory method to create a new instance of PasswordValidator.
     *
     * @return PasswordValidator
     */
    public static function create(): PasswordValidator
    {
        return new PasswordValidator();
    }

    /**
     * Handle the validation logic.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        // Ensure the password is not empty
        if (empty($value)) {
            $fail("The $attribute field is required.");
            return;
        }

        // Check if the password contains at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $fail("The $attribute must contain at least one uppercase letter.");
            return;
        }

        // Check if the password contains at least one number
        if (!preg_match('/\d/', $value)) {
            $fail("The $attribute must contain at least one number.");
            return;
        }

        // Check if the password contains at least one special character
        if (!preg_match('/[^\w]/', $value)) {
            $fail("The $attribute must contain at least one special character.");
            return;
        }

        // Check if the password length is greater than 4 characters
        if (strlen($value) <= 4) {
            $fail("The $attribute must be longer than 4 characters.");
            return;
        }
    }
}
