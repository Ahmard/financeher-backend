<?php

namespace App\Repositories;

use App\Models\UserBusinessStage;
use App\QueryBuilders\UserBusinessStageQueryBuilder;
use App\QueryBuilders\UserBusinessTypeQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class UserBusinessStageRepository extends BaseRepository
{
    public function __construct(
        private readonly UserBusinessStageQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $typeId,
    ): UserBusinessStage|Model
    {
        return UserBusinessStage::query()->create([
            'created_by' => $createdBy,
            'user_id' => $userId,
            'business_stage_id' => $typeId,
        ]);
    }

    public function queryBuilder(): UserBusinessTypeQueryBuilder
    {
        return $this->queryBuilder;
    }
}