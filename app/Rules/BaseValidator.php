<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

abstract class BaseValidator implements InvokableRule
{
    abstract public function __invoke($attribute, $value, $fail): void;
}
