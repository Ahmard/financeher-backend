<?php

namespace App\Repositories;

use App\Models\UserBusinessType;
use App\QueryBuilders\UserBusinessTypeQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class UserBusinessTypeRepository extends BaseRepository
{
    public function __construct(
        private readonly UserBusinessTypeQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $typeId,
    ): UserBusinessType|Model {
        return UserBusinessType::query()->create([
            'created_by' => $createdBy,
            'user_id' => $userId,
            'business_type_id' => $typeId,
        ]);
    }

    public function queryBuilder(): UserBusinessTypeQueryBuilder
    {
        return $this->queryBuilder;
    }
}
