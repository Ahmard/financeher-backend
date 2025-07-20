<?php

namespace App\Enums;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum UserRole implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case ADMIN;
    case SUPER_ADMIN;
    case USER;
}
