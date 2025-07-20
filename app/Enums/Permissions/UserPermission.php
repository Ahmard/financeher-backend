<?php

namespace App\Enums\Permissions;

use App\Enums\EnumHelper\HelperTrait;
use App\Enums\EnumHelper\PermissionInterface;

enum UserPermission implements PermissionInterface
{
    use HelperTrait;

    case USER_LIST;
    case USER_CREATE;
    case USER_READ;
    case USER_UPDATE;
    case USER_DELETE;

    case USER_VERIFY;
    case USER_ACTIVATE;
    case USER_DEACTIVATE;
    case USER_RESET_PASSWORD;

    case USER_ROLE_LIST;
    case USER_ROLE_ADD;
    case USER_ROLE_REMOVE;

    case USER_PERMISSION_LIST;
    case USER_PERMISSION_ADD;
    case USER_PERMISSION_REMOVE;
}
