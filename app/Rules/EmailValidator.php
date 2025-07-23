<?php

namespace App\Rules;

use Illuminate\Support\Facades\App;

class EmailValidator
{
    public static function rules(): string
    {
        if (App::isProduction()) {
            return 'required|email:rfc,dns|unique:users,email';
        }

        return 'required|email|unique:users,email';
    }
}
