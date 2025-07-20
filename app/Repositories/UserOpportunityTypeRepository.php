<?php

namespace App\Repositories;

use App\Models\UserOpportunityType;
use App\QueryBuilders\UserOpportunityTypeQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class UserOpportunityTypeRepository extends BaseRepository
{
    public function __construct(
        private readonly UserOpportunityTypeQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $typeId,
    ): UserOpportunityType|Model
    {
        return UserOpportunityType::query()->create([
            'created_by' => $createdBy,
            'user_id' => $userId,
            'opportunity_type_id' => $typeId,
        ]);
    }

    public function queryBuilder(): UserOpportunityTypeQueryBuilder
    {
        return $this->queryBuilder;
    }
}