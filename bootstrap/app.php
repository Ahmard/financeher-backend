<?php

use App\Exceptions\Handler as ExceptionHandler;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\SetAuthTokenMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api/v1')
                ->middleware('api')
                ->name('api.v1.')
                ->group(__DIR__ . '/../routes/api.php');

            Route::prefix('whk/v1')
                ->name('whk.v1.')
                ->group(__DIR__ . '/../routes/webhook.php');
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SetAuthTokenMiddleware::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['auth.perm' => AuthMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        ExceptionHandler::handle($exceptions);
    })
    ->create();
