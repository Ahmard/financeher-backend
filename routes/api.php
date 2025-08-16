<?php

use App\Http\Controllers\MiscController;
use App\Http\Controllers\OpportunityController;
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


    Route::prefix('opportunities')->group(function () {
        Route::get('/', [OpportunityController::class, 'index']);
        Route::get('/saved-items', [OpportunityController::class, 'savedItems']);
        Route::post('/saved-items', [OpportunityController::class, 'saveItem']);
        Route::delete('/saved-items/{oid}', [OpportunityController::class, 'removeItem']);
        Route::get('/{id}', [OpportunityController::class, 'show']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);

        Route::post('/create-intent', [PaymentController::class, 'createPaymentIntent']);
        Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
        Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession']);
        Route::post('/verify-checkout-session', [PaymentController::class, 'verifyCheckoutSession']);

        Route::post('/{ref}/verify', [PaymentController::class, 'verify']);
    });
});

# MISCELLANEOUS
Route::prefix('misc')->group(function () {
    Route::get('opportunity-filters', [MiscController::class, 'opportunityFilters']);
    Route::get('active-plan', [MiscController::class, 'activePlan']);
    Route::get('industries', [MiscController::class, 'industries']);
    Route::get('business-stages', [MiscController::class, 'businessStages']);
    Route::get('opportunity-types', [MiscController::class, 'opportunityTypes']);
    Route::get('account-setup-finalise-data', [MiscController::class, 'accountSetupFinaliseData']);

    Route::get('geo/countries', [MiscController::class, 'countries']);
    Route::get('geo/countries/{id}/states', [MiscController::class, 'states']);
    Route::get('geo/states/{id}/local-govs', [MiscController::class, 'localGovs']);
});
