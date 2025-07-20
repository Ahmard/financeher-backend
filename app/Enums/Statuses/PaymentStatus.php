<?php

namespace App\Enums\Statuses;

use App\Enums\EnumHelper\DBCompatibleEnumTrait;
use App\Enums\EnumHelper\StatusEnumInterface;

enum PaymentStatus implements StatusEnumInterface
{
    use DBCompatibleEnumTrait;

    case PENDING;
    case PAID;
    case CANCELED;
    case EXPIRED;
}
