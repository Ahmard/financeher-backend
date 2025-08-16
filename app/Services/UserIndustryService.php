<?php

namespace App\Services;

use App\Models\UserIndustry;
use App\Repositories\UserIndustryRepository;
use Illuminate\Database\Eloquent\Model;

class UserIndustryService extends BasePersistableService
{
    public function __construct(
        private readonly UserIndustryRepository $repository,
    ) {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $typeId,
    ): UserIndustry|Model {
        return $this->repository->create(
            createdBy: $createdBy,
            userId: $userId,
            typeId: $typeId,
        );
    }

    public function delete(string $id, string $userId): void
    {
        $type = $this->repository
            ->clone()
            ->withOwnerId($userId)
            ->findRequiredById($id);

        $this->repository->deleteById($type['id']);
    }

    public function repository(): UserIndustryRepository
    {
        return $this->repository;
    }
}
