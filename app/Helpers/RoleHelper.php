<?php

namespace App\Helpers;

use App\Enums\Permissions\PaymentPermission;
use App\Enums\Permissions\PermissionPermission;
use App\Enums\Permissions\RolePermission;
use App\Enums\Permissions\UserPermission;
use UnitEnum;

class RoleHelper
{
    private static array $permissions = [
        UserPermission::class,
        RolePermission::class,
        PaymentPermission::class,
        PermissionPermission::class,
    ];

    /**
     * @return string[]
     */
    public static function getPermissionNames(): array
    {
        $permissions = [];

        foreach (self::getPermissions() as $permission) {
            foreach ($permission::cases() as $enum) {
                $permissions[] = strtolower($enum->name);
            }
        }

        return $permissions;
    }

    /**
     * @return UnitEnum[]
     */
    public static function getPermissions(): array
    {
        return self::$permissions;
    }
}
