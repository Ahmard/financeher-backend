<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class CacheService extends BaseService
{
    // clear all cache
    public static function clear(): void
    {

        Artisan::call('cache:forget spatie.permission.cache');
        Artisan::call('cache:clear');
    }
}