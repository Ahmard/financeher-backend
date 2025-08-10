<?php

namespace App\Enums\Permissions;

use App\Enums\EnumHelper\HelperTrait;
use App\Enums\EnumHelper\PermissionInterface;

enum OpportunityPermission implements PermissionInterface
{
    use HelperTrait;

    case OPPORTUNITY_LIST;
    case OPPORTUNITY_CREATE;
    case OPPORTUNITY_READ;
    case OPPORTUNITY_UPDATE;
    case OPPORTUNITY_DELETE;
}
