<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class BaseCommand extends Command
{
    public function info($string, $verbosity = null)
    {
        Log::info($string);
        parent::info($string, $verbosity);
    }

    public function comment($string, $verbosity = null)
    {
        Log::info($string);
        parent::comment($string, $verbosity);
    }

    public function error($string, $verbosity = null)
    {
        Log::error($string);
        parent::error($string, $verbosity);
    }

    public function infoScoped(string $message, string|int|null $scope = null): void
    {
        $this->info($this->makeScopedMessage($message, $scope));
    }

    public function commentScoped(string $message, string|int|null $scope = null): void
    {
        $this->comment($this->makeScopedMessage($message, $scope));
    }

    public function errorScoped(string $message, string|int|null $scope = null): void
    {
        $this->error($this->makeScopedMessage($message, $scope));
    }

    public function handleException(Throwable $throwable, string|int|null $scope = null): void
    {
        $this->error($this->makeScopedMessage((string)$throwable, $scope));
    }

    private function makeScopedMessage(string $message, string|int|null $scope = null): string
    {
        $array = explode('\\', static::class);
        if ($scope) {
            return sprintf('[%s][%s] %s', end($array), $scope, $message);
        } else {
            return sprintf('[%s] %s', end($array), $message);
        }
    }
}
