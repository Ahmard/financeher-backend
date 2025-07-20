<?php

namespace App\Enums\EnumHelper;

interface PermissionInterface
{
    public static function name(): string;

    public static function customMiddlewarePermission(string $name): array;

    public function toLowercase(): string;

    public function middlewarePermission(): array;
}
