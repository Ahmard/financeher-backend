<?php

namespace App\Repositories;

use App\Enums\Types\WalletAction;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\QueryBuilders\WalletHistoryQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class WalletHistoryRepository extends BaseRepository
{
    public function __construct(
        private readonly WalletHistoryQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        Wallet|Model $wallet,
        float        $amount,
        string       $narration,
        WalletAction $action
    ): WalletHistory|Model
    {
        return WalletHistory::query()->create([
            'wallet_id' => $wallet['id'],
            'balance_before' => $wallet['balance'],
            'amount' => $amount,
            'balance_after' => $action === WalletAction::CREDIT
                ? $wallet['balance'] + $amount
                : $wallet['balance'] - $amount,
            'narration' => $narration,
            'action' => $action->lowercase()
        ]);
    }

    public function queryBuilder(): WalletHistoryQueryBuilder
    {
        return $this->queryBuilder;
    }
}
