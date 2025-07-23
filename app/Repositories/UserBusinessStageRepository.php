<?php

namespace App\Repositories;

use App\Models\UserBusinessStage;
use App\QueryBuilders\UserBusinessStageQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class UserBusinessStageRepository extends BaseRepository
{
    public function __construct(
        private readonly UserBusinessStageQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $stageId,
    ): UserBusinessStage|Model {
        return UserBusinessStage::query()->create([
            'created_by' => $createdBy,
            'user_id' => $userId,
            'business_stage_id' => $stageId,
        ]);
    }

    public function queryBuilder(): UserBusinessStageQueryBuilder
    {
        return $this->queryBuilder;
    }
}
