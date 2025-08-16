<?php

namespace App\Repositories;

use App\Models\AppliedOpportunity;
use App\QueryBuilders\AppliedOpportunityQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class AppliedOpportunityRepository extends BaseRepository
{
    public function __construct(
        private readonly AppliedOpportunityQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(int $userId, string $oppId): Model|AppliedOpportunity
    {
        return AppliedOpportunity::query()->create([
            'user_id' => $userId,
            'opportunity_id' => $oppId,
        ]);
    }

    public function isApplied(int $userId, string $oppId): AppliedOpportunity|Model|null
    {
        return AppliedOpportunity::query()
            ->where('user_id', $userId)
            ->where('opportunity_id', $oppId)
            ->first();
    }

    public function queryBuilder(): AppliedOpportunityQueryBuilder
    {
        return $this->queryBuilder;
    }
}
