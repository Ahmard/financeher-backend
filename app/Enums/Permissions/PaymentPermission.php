<?php

namespace App\Enums\Permissions;

use App\Enums\EnumHelper\HelperTrait;
use App\Enums\EnumHelper\PermissionInterface;

enum PaymentPermission implements PermissionInterface
{
    use HelperTrait;

    case PAYMENT_LIST;
    case PAYMENT_CREATE;
    case PAYMENT_READ;
    case PAYMENT_UPDATE;
    case PAYMENT_DELETE;
}
