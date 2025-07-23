<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\PasswordResetController;
use App\Http\Controllers\Authentication\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('login', [LoginController::class, 'login']);

Route::get('me', [LoginController::class, 'me']);

Route::post('register', [RegisterController::class, 'register']);
Route::post('register/resend-account-verification-email', [RegisterController::class, 'resendAccountVerificationEmail']);
Route::post('register/{uuid}/verify-email', [RegisterController::class, 'verifyEmail']);
Route::get('register/{uuid}/verify-token-validity', [RegisterController::class, 'verifyTokenValidity']);

Route::prefix('password-reset')->group(function () {
    Route::post('', [PasswordResetController::class, 'initiate']);
    Route::get('{token}/verify-token-validity', [PasswordResetController::class, 'canReset']);
    Route::post('{token}/reset', [PasswordResetController::class, 'resetPassword']);
});
