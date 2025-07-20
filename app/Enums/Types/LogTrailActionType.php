<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum LogTrailActionType implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case CREATE;
    case READ;
    case UPDATE;
    case DELETE;
}
