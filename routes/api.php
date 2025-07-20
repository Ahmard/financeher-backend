<?php

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
});
