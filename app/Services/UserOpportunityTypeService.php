<?php

namespace App\Services;

use App\Models\UserOpportunityType;
use App\Repositories\UserOpportunityTypeRepository;
use Illuminate\Database\Eloquent\Model;

class UserOpportunityTypeService extends BasePersistableService
{
    public function __construct(
        private readonly UserOpportunityTypeRepository $repository,
    ) {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $typeId,
    ): UserOpportunityType|Model {
        return $this->repository->create(
            createdBy: $createdBy,
            userId: $userId,
            typeId: $typeId,
        );
    }

    public function delete(string $id, string $userId): void
    {
        $opp = $this->repository
            ->clone()
            ->withOwnerId($userId)
            ->findRequiredById($id);

        $this->repository->deleteById($opp['id']);
    }

    public function repository(): UserOpportunityTypeRepository
    {
        return $this->repository;
    }
}
