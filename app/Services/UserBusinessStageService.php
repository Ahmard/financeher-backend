<?php

namespace App\Services;

use App\Models\UserBusinessStage;
use App\Repositories\BaseRepository;
use App\Repositories\UserBusinessStageRepository;
use Illuminate\Database\Eloquent\Model;

class UserBusinessStageService extends BasePersistableService
{
    public function __construct(
        private readonly UserBusinessStageRepository $repository,
    ) {
    }

    public function create(
        int    $createdBy,
        int    $userId,
        string $stageId,
    ): UserBusinessStage|Model {
        return $this->repository->create(
            createdBy: $createdBy,
            userId: $userId,
            stageId: $stageId,
        );
    }

    public function delete(string $id, string $userId): void
    {
        $stage = $this->repository
            ->clone()
            ->withOwnerId($userId)
            ->findRequiredById($id);

        $this->repository->deleteById($stage['id']);
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}
