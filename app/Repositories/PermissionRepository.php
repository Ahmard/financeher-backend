<?php

namespace App\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\Models\Permission;
use App\QueryBuilders\PermissionQueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PermissionRepository extends BaseRepository
{
    public function __construct(
        public readonly PermissionQueryBuilder $queryBuilder,
    ) {
    }

    public function fetchName(int $id): string
    {
        $name = Permission::query()
            ->select('name')
            ->where('id', $id)
            ->value('name');

        if (!$name) {
            throw new ModelNotFoundException('Such permission does not exists');
        }

        return $name;
    }


    /**
     * @param int $roleId
     * @param string|null $searchQuery
     * @return Collection<int, Permission|Model>
     */
    public function fetchRoleAssignable(int $roleId, ?string $searchQuery): Collection
    {
        return $this
            ->queryBuilder()
            ->withSearch($searchQuery)
            ->filterRoleAssignable($roleId)
            ->get();
    }

    public function queryBuilder(): PermissionQueryBuilder
    {
        return $this->queryBuilder;
    }
}
