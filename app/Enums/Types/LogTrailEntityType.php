<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum LogTrailEntityType implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case MULTIPURPOSE_USE;

    case USER;
    case WALLET;
    case DESIGN_PROFILE;
    case UPLOADED_FILE;
    case SYSTEM_SETTINGS;
    case ONBOARDING_SETTINGS;
    case BUSINESS_STAGE;
    case BUSINESS_TYPE;
    case OPPORTUNITY_TYPE;
    case USER_BUSINESS_STAGE;
    case USER_BUSINESS_TYPE;
    case USER_OPPORTUNITY_TYPE;
}
