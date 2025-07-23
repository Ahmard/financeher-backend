<?php

use App\Enums\Permissions\PaymentPermission;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

// PAYMENTS
Route::get('payments', [PaymentController::class, 'index'])
    ->middleware(PaymentPermission::PAYMENT_LIST->middlewarePermission());
Route::post('payments/{ref}/verify', [PaymentController::class, 'verify'])
    ->middleware(PaymentPermission::PAYMENT_UPDATE->middlewarePermission());

// USER MANAGEMENT
// ROLES
Route::apiResource('roles', RoleController::class);

Route::get('roles/{id}/permissions', [RoleController::class, 'permissions']);
Route::post('roles/{id}/permissions', [RoleController::class, 'permissionsAssign']);
Route::get('roles/{id}/permissions/assignable', [RoleController::class, 'permissionsAssignable']);
Route::delete('roles/{id}/permissions/{permName}', [RoleController::class, 'permissionsUnassign']);

// PERMISSIONS
Route::apiResource('permissions', PermissionController::class);

// USERS
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/active', [UserController::class, 'active']);
    Route::get('/suspended', [UserController::class, 'suspended']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::get('{id}/roles', [UserController::class, 'roles']);
    Route::post('{id}/roles', [UserController::class, 'assignRoles']);
    Route::patch('{id}/activate', [UserController::class, 'activate']);
    Route::patch('{id}/deactivate', [UserController::class, 'deactivate']);
    Route::get('{id}/permissions', [UserController::class, 'permissions']);
    Route::get('{id}/account-verification-url', [UserController::class, 'accountVerificationUrl']);
    Route::post('{id}/resend-verification-email', [UserController::class, 'resendVerificationEmail']);
    Route::delete('{id}/roles/{rid}', [UserController::class, 'unassignRole']);
});
