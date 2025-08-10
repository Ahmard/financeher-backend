<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum BillingCycleKind implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case MONTHLY;

    case QUARTERLY;

    case YEARLY;
}
