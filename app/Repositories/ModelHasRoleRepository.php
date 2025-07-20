<?php

namespace App\Repositories;

use App\Models\ModelHasRole;
use App\QueryBuilders\ModelHasRoleQueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ModelHasRoleRepository extends BaseRepository
{
    public function __construct(
        public readonly ModelHasRoleQueryBuilder $queryBuilder,
    ) {
    }

    /**
     * @param int $userId
     * @return Collection<int, ModelHasRole|Model>
     */
    public function getUserRoles(int $userId): Collection
    {
        return $this
            ->queryBuilder()
            ->filterByUser($userId)
            ->get();
    }

    public function userHasRole(int $userId, int $roleId): bool
    {
        return ModelHasRole::query()
            ->where('role_id', $roleId)
            ->where('model_id', $userId)
            ->exists();
    }

    public function queryBuilder(): ModelHasRoleQueryBuilder
    {
        return $this->queryBuilder;
    }
}
