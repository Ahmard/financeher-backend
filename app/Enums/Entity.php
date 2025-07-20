<?php

namespace App\Enums;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum Entity implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case USER;
}
