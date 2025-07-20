<?php

namespace App\QueryBuilders;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;

class WalletQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return Wallet::query();
    }
}
