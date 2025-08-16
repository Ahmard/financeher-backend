<?php

namespace App\Services;

use App\Exceptions\WarningException;
use App\Models\AppliedOpportunity;
use App\Repositories\AppliedOpportunityRepository;
use Illuminate\Database\Eloquent\Model;

class AppliedOpportunityService extends BasePersistableService
{
    public function __construct(
        private readonly AppliedOpportunityRepository $repository,
    )
    {
    }

    public function create(int $userId, string $oppId): Model|AppliedOpportunity
    {
        $applied = $this->repository->isApplied($userId, $oppId);
        if ($applied) {
            return $applied;
        }

        return $this->repository->create(
            userId: $userId,
            oppId: $oppId,
        );
    }

    public function remove(int $ownerId, string $oppId): void
    {
        AppliedOpportunity::query()
            ->where('opportunity_id', $oppId)
            ->where('user_id', $ownerId)
            ->delete();
    }

    public function repository(): AppliedOpportunityRepository
    {
        return $this->repository;
    }
}
