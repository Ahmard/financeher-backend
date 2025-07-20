<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum WalletAction implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case DEBIT;
    case CREDIT;
}
