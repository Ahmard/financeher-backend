<?php

namespace App\Helpers;

use Illuminate\Support\Carbon as LaravelCarbon;
use Illuminate\Support\Facades\App;

class Carbon
{
    public static function now(): LaravelCarbon
    {
        return match (App::isLocal()) {
            true => LaravelCarbon::now()->addHour(),
            false => LaravelCarbon::now(),
        };
    }
}
