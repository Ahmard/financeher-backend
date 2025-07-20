<?php

namespace App\Enums;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum HttpRequestPurpose implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case FORM_SELECT;
}
