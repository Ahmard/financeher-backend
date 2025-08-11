<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum PaymentPurpose implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case WALLET_FUNDING;
    case PLAN_SUBSCRIPTION;
}
