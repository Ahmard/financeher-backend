<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum LoanVcKind implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case LOAN;
    case VENTURE_CAPITAL;
}
