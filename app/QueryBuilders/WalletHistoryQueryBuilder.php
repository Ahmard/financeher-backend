<?php

namespace App\QueryBuilders;

use App\Models\WalletHistory;
use Illuminate\Database\Eloquent\Builder;

class WalletHistoryQueryBuilder extends BaseQueryBuilder
{
    public function filterByUserId(int $userId): Builder
    {
        return $this
            ->all()
            ->where('wallets.user_id', $userId);
    }

    protected function builder(): Builder
    {
        return WalletHistory::query()
            ->select(['wallet_histories.*'])
            ->join('wallets', 'wallet_histories.wallet_id', '=', 'wallets.id');
    }
}
