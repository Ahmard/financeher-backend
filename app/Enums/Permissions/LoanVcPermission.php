<?php

namespace App\Enums\Permissions;

use App\Enums\EnumHelper\HelperTrait;
use App\Enums\EnumHelper\PermissionInterface;

enum LoanVcPermission implements PermissionInterface
{
    use HelperTrait;

    case LOAN_VC_LIST;
    case LOAN_VC_CREATE;
    case LOAN_VC_READ;
    case LOAN_VC_UPDATE;
    case LOAN_VC_DELETE;
}
