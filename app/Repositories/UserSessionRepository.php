<?php

namespace App\Repositories;

use App\Models\UserSession;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\UserSessionQueryBuilder;
use App\Repositories\Traits\UuidRepositoryTrait;
use Illuminate\Database\Eloquent\Model;

class UserSessionRepository extends BaseRepository
{
    use UuidRepositoryTrait;

    public function __construct(
        private readonly UserSessionQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(int $userId, string $jti): UserSession|Model
    {
        return UserSession::query()->create([
            'user_id' => $userId,
            'jti' => $jti,
            'is_active' => true,
        ]);
    }

    public function isJwtUsable(string $jti): bool
    {
        return UserSession::query()
            ->where('jti', $jti)
            ->where('is_active', true)
            ->exists();
    }

    public function logoutOtherDevices(int $userId): void
    {
        UserSession::query()
            ->where('user_id', $userId)
            ->update(['is_active' => false]);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}