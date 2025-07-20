<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum SystemMessageType implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case INFO;
    case WARNING;
    case ERROR;
}
