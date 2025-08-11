<?php

use App\Http\Controllers\MiscController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(fn() => require 'api/auth.php');
Route::prefix('admin')
    ->middleware('auth.perm')
    ->group(fn() => require 'api/admin.php');

Route::middleware('auth.perm')->group(fn() => require 'api/user.php');

Route::middleware('auth.perm')->group(function () {
    Route::get('profile', [ProfileController::class, 'info']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::post('profile/picture', [ProfileController::class, 'uploadProfilePicture']);

    Route::get('payments', [PaymentController::class, 'index']);
    Route::post('payments/create-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('payments/{ref}/verify', [PaymentController::class, 'verify']);
});

# MISCELLANEOUS
Route::prefix('misc')->group(function () {
    Route::get('active-plan', [MiscController::class, 'activePlan']);
    Route::get('business-types', [MiscController::class, 'businessTypes']);
    Route::get('business-stages', [MiscController::class, 'businessStages']);
    Route::get('opportunity-types', [MiscController::class, 'opportunityTypes']);

    Route::get('geo/countries', [MiscController::class, 'countries']);
    Route::get('geo/countries/{id}/states', [MiscController::class, 'states']);
    Route::get('geo/states/{id}/local-govs', [MiscController::class, 'localGovs']);
});
