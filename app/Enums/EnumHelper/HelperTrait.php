<?php

namespace App\Enums\EnumHelper;

use Illuminate\Support\Str;

trait HelperTrait
{
    public static function customMiddlewarePermission(string $name): array
    {
        $entity = str_replace('_permission', '', self::name());
        return [sprintf('auth.perm:%s_%s', $entity, $name)];
    }

    public static function name(): string
    {
        $expClass = explode('\\', static::class);
        return Str::snake(end($expClass));
    }

    public function middlewarePermission(): array
    {
        return ["auth.perm:{$this->toLowercase()}"];
    }

    public function toLowercase(): string
    {
        return strtolower($this->name);
    }
}
