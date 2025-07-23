<?php

namespace App\Repositories;

use App\Helpers\Carbon;
use App\Models\PasswordReset;
use App\Models\User;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\PasswordResetQueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PasswordResetRepository extends BaseRepository
{
    public function __construct(
        private readonly PasswordResetQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        string                     $email,
        string                     $token,
        \Illuminate\Support\Carbon $expiry
    ): PasswordReset|Model {
        return PasswordReset::query()->create([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiry,
            'created_at' => Carbon::now()
        ]);
    }

    public function findByToken(string $token): Model|User|null
    {
        $builder = $this->getQueryBuilder()
            ->all()
            ->where('password_reset_tokens.token', $token);

        return $this->getEloquentBuilder($builder)->first();
    }

    public function findByEmail(string $email): Model|User|null
    {
        return PasswordReset::query()
            ->where('password_reset_tokens.email', $email)
            ->first();
    }

    public function existsByToken(string $token): bool
    {
        $builder = $this->getQueryBuilder()
            ->withSelect(['password_reset_tokens.token'])
            ->all()
            ->where('password_reset_tokens.token', $token);

        return $this->getEloquentBuilder($builder)->exists();
    }

    public function deleteByEmail(string $email): void
    {
        DB::select('DELETE FROM password_reset_tokens WHERE email = ?', [$email]);
    }

    public function canReset(string $token): bool
    {
        return !empty($this->findByToken($token));
    }

    /**
     * @return void
     */
    public function deleteExpiredTokens(): void
    {
        DB::select('DELETE FROM password_reset_tokens WHERE expires_at < ?', [Carbon::now()]);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}
