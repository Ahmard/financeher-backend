<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// PAYMENT
Route::prefix('payments')->group(function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::get('/{id}', [PaymentController::class, 'show']);
    Route::post('/{ref}/verify', [PaymentController::class, 'verify']);
    Route::post('/{ref}/query', [PaymentController::class, 'verify']);
});

// WALLET
Route::prefix('wallet')->group(function () {
    Route::get('/', [WalletController::class, 'index']);
    Route::get('/history', [WalletController::class, 'history']);
    Route::post('/init-funding', [WalletController::class, 'initFunding']);
});
