<?php

namespace App\Enums\Permissions;

use App\Enums\EnumHelper\HelperTrait;
use App\Enums\EnumHelper\PermissionInterface;

enum PlanPermission implements PermissionInterface
{
    use HelperTrait;

    case PLAN_LIST;
    case PLAN_CREATE;
    case PLAN_READ;
    case PLAN_UPDATE;
    case PLAN_DELETE;
}
