<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum PaymentGateway implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case PAYSTACK;
    case MONNIFY;
    case SQUAD;
    case REMITTA;
}
