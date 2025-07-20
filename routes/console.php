<?php

use App\Console\Commands\DeleteExpiredPasswordResetTokenCommand;
use App\Console\Commands\UserActivateLightlySuspendedCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(DeleteExpiredPasswordResetTokenCommand::class)
    ->everyFiveMinutes();

Schedule::command(UserActivateLightlySuspendedCommand::class)
    ->everyMinute();
