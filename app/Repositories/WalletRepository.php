<?php

namespace App\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\Models\Wallet;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\WalletQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class WalletRepository extends BaseRepository
{
    public function __construct(
        private readonly WalletQueryBuilder $queryBuilder,
    ) {
    }

    public function create(int $userId): Wallet|Model
    {
        return Wallet::query()->create([
            'user_id' => $userId,
            'balance' => 0.0,
        ]);
    }

    public function findRequiredByUserId(int $id): Wallet|Model
    {
        $wallet = $this
            ->queryBuilder()
            ->all()
            ->where('wallets.user_id', $id)
            ->first();

        if (null == $wallet) {
            throw new ModelNotFoundException('wallet not found');
        }

        return $wallet;
    }

    public function findLockedByUserId(int $userId): Wallet|Model
    {
        $wallet = $this
            ->queryBuilder()
            ->all()
            ->where('wallets.user_id', $userId)
            ->lockForUpdate()
            ->first();

        if (null == $wallet) {
            throw new ModelNotFoundException('wallet not found');
        }

        return $wallet;
    }

    public function fetchBalance(int $userId): float
    {
        $balance = Wallet::query()
            ->select('balance')
            ->where('wallets.user_id', $userId)
            ->value('balance');

        if (null === $balance) {
            throw new ModelNotFoundException('wallet not found');
        }

        return $balance;
    }

    /**
     * @param int $userId
     * @param float $amount
     * @return Wallet|Model
     * @throws ModelNotFoundException
     */
    public function credit(int $userId, float $amount): Wallet|Model
    {
        $wallet = $this->findRequiredByUserId($userId);

        $wallet->increment('balance', $amount);
        return $wallet;
    }

    /**
     * @param int $userId
     * @param float $amount
     * @return Wallet|Model
     * @throws ModelNotFoundException
     */
    public function debit(int $userId, float $amount): Wallet|Model
    {
        $wallet = $this->findRequiredByUserId($userId);
        $wallet->decrement('balance', $amount);
        return $wallet;
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}
