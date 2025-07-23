<?php

namespace App\Services;

use App\Models\UserBusinessType;
use App\Repositories\UserBusinessTypeRepository;
use Illuminate\Database\Eloquent\Model;

class UserBusinessTypeService extends BasePersistableService
{
    public function __construct(
        private readonly UserBusinessTypeRepository $repository,
    )
    {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $typeId,
    ): UserBusinessType|Model
    {
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

    public function repository(): UserBusinessTypeRepository
    {
        return $this->repository;
    }
}