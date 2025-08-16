<?php

namespace App\Repositories;

use App\Models\SavedOpportunity;
use App\QueryBuilders\SavedOpportunityQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class SavedOpportunityRepository extends BaseRepository
{
    public function __construct(
        private readonly SavedOpportunityQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(int $userId, string $oppId): Model|SavedOpportunity
    {
        return SavedOpportunity::query()->create([
            'user_id' => $userId,
            'opportunity_id' => $oppId,
        ]);
    }

    public function isSaved(int $userId, string $oppId): bool
    {
        return SavedOpportunity::query()
            ->where('user_id', $userId)
            ->where('opportunity_id', $oppId)
            ->exists();
    }

    public function queryBuilder(): SavedOpportunityQueryBuilder
    {
        return $this->queryBuilder;
    }
}
