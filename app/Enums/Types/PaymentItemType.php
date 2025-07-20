<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum PaymentItemType implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case PETTY_CASH;
}
