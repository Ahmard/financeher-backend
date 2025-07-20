<?php

namespace App\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\RoleQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository
{
    public function __construct(
        public readonly RoleQueryBuilder $queryBuilder,
    ) {
    }

    public function findRequiredByName(string $name): Model|Role
    {
        $role = $this
            ->queryBuilder()
            ->all()
            ->where('roles.name', $name)
            ->first();

        if ($role == null) {
            throw new ModelNotFoundException('Such role does not exists');
        }

        return $role;
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}
