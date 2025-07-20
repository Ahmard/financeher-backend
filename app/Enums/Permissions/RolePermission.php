<?php

namespace App\Enums\Permissions;

use App\Enums\EnumHelper\HelperTrait;
use App\Enums\EnumHelper\PermissionInterface;

enum RolePermission implements PermissionInterface
{
    use HelperTrait;

    case ROLE_LIST;
    case ROLE_CREATE;
    case ROLE_READ;
    case ROLE_UPDATE;
    case ROLE_DELETE;
}
