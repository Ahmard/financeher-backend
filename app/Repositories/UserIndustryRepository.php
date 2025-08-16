<?php

namespace App\Repositories;

use App\Models\UserIndustry;
use App\QueryBuilders\UserIndustryQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class UserIndustryRepository extends BaseRepository
{
    public function __construct(
        private readonly UserIndustryQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $typeId,
    ): UserIndustry|Model {
        return UserIndustry::query()->create([
            'created_by' => $createdBy,
            'user_id' => $userId,
            'business_type_id' => $typeId,
        ]);
    }

    public function queryBuilder(): UserIndustryQueryBuilder
    {
        return $this->queryBuilder;
    }
}
