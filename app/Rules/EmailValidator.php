<?php

namespace App\Rules;

use Illuminate\Support\Facades\App;

class EmailValidator
{
    public static function rules(bool $uniqueEmail = false): string
    {
        $uniqueCheck = ($uniqueEmail ? '|unique:users,email' : '');

        if (App::isProduction()) {
            return 'required|email:rfc,dns' . $uniqueCheck;
        }

        return 'required|email' . $uniqueCheck;
    }
}
