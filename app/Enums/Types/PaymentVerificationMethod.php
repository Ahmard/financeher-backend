<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum PaymentVerificationMethod implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case WEBHOOK;
    case POLLING;
}
