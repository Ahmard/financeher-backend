<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum PaymentMethod implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case ONLINE;
    case TRANSFER;
    case CASH;
}
