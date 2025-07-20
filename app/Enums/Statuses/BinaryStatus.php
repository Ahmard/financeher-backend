<?php

namespace App\Enums\Statuses;

use App\Enums\EnumHelper\DBCompatibleEnumTrait;
use App\Enums\EnumHelper\StatusEnumInterface;

enum BinaryStatus implements StatusEnumInterface
{
    use DBCompatibleEnumTrait;

    case ACTIVE;
    case INACTIVE;
}
