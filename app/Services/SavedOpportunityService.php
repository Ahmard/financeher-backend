<?php

namespace App\Services;

use App\Exceptions\WarningException;
use App\Models\SavedOpportunity;
use App\Repositories\SavedOpportunityRepository;
use Illuminate\Database\Eloquent\Model;

class SavedOpportunityService extends BasePersistableService
{
    public function __construct(
        private readonly SavedOpportunityRepository $repository,
    )
    {
    }

    public function create(int $userId, string $oppId): Model|SavedOpportunity
    {
        if ($this->repository->isSaved($userId, $oppId)) {
            throw new WarningException('Opportunity already saved');
        }

        return $this->repository->create(
            userId: $userId,
            oppId: $oppId,
        );
    }

    public function remove(int $ownerId, string $oppId): void
    {
        SavedOpportunity::query()
            ->where('opportunity_id', $oppId)
            ->where('user_id', $ownerId)
            ->delete();
    }

    public function repository(): SavedOpportunityRepository
    {
        return $this->repository;
    }
}
