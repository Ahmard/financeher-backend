<?php

namespace App\Helpers;

use App\Enums\EnumHelper\PermissionInterface;
use Illuminate\Support\Facades\Route;

class RouteHelper
{
    /**
     * @param string $name
     * @param string $controller
     * @param class-string<PermissionInterface> $permission
     * @param bool $withStatus
     * @return void
     */
    public static function apiResourceWithApproval(
        string $name,
        string $controller,
        string $permission,
        bool   $withStatus = false
    ): void
    {
        if ($withStatus) {
            self::apiResourceWithStatus($name, $controller, $permission);
        } else {
            self::apiResource($name, $controller, $permission);
        }

        Route::patch("$name/{id}/approve", [$controller, 'approve'])->middleware($permission::customMiddlewarePermission('approve'));
        Route::patch("$name/{id}/reject", [$controller, 'reject'])->middleware($permission::customMiddlewarePermission('reject'));
    }

    /**
     * @param string $name
     * @param string $controller
     * @param class-string<PermissionInterface> $permission
     * @return void
     */
    public static function apiResourceWithStatus(
        string $name,
        string $controller,
        string $permission
    ): void
    {
        self::apiResource($name, $controller, $permission);
        Route::patch("$name/{id}/activate", [$controller, 'activate'])->middleware($permission::customMiddlewarePermission('activate'));
        Route::patch("$name/{id}/deactivate", [$controller, 'deactivate'])->middleware($permission::customMiddlewarePermission('deactivate'));
    }

    /**
     * @param string $name
     * @param string $controller
     * @param class-string<PermissionInterface> $permission
     * @return void
     */
    public static function apiResource(
        string $name,
        string $controller,
        string $permission
    ): void
    {
        Route::get($name, [$controller, 'index'])->middleware($permission::customMiddlewarePermission('list'));
        Route::post($name, [$controller, 'store'])->middleware($permission::customMiddlewarePermission('create'));
        Route::get("$name/{id}", [$controller, 'show'])->middleware($permission::customMiddlewarePermission('read'));
        Route::put("$name/{id}", [$controller, 'update'])->middleware($permission::customMiddlewarePermission('update'));
        Route::patch("$name/{id}", [$controller, 'update'])->middleware($permission::customMiddlewarePermission('update'));
        Route::delete("$name/{id}", [$controller, 'destroy'])->middleware($permission::customMiddlewarePermission('delete'));
    }
}
