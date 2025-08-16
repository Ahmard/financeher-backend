<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum UserRegistrationStage implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case EMAIL_VERIFICATION;
    case PLAN_SUBSCRIPTION;
    case ACCOUNT_SETUP;
    case COMPLETED;
}
