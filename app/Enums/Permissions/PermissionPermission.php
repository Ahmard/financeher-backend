<?php

namespace App\Enums\Permissions;

use App\Enums\EnumHelper\HelperTrait;
use App\Enums\EnumHelper\PermissionInterface;

enum PermissionPermission implements PermissionInterface
{
    use HelperTrait;

    case PERMISSION_LIST;
    case PERMISSION_CREATE;
    case PERMISSION_READ;
    case PERMISSION_UPDATE;
    case PERMISSION_DELETE;
}
