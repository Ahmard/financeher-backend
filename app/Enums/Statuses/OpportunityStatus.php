<?php

namespace App\Enums\Statuses;

use App\Enums\EnumHelper\DBCompatibleEnumTrait;
use App\Enums\EnumHelper\StatusEnumInterface;

enum OpportunityStatus implements StatusEnumInterface
{
    use DBCompatibleEnumTrait;

    case ONGOING;
    case CLOSED;
}
