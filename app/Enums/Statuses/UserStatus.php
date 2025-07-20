<?php

namespace App\Enums\Statuses;

use App\Enums\EnumHelper\DBCompatibleEnumTrait;
use App\Enums\EnumHelper\StatusEnumInterface;

enum UserStatus implements StatusEnumInterface
{
    use DBCompatibleEnumTrait;

    case ACTIVE;
    case INACTIVE;
    case PENDING;
}
