<?php

namespace App\Services;

use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Models\BusinessStage;
use App\Repositories\BaseRepository;
use App\Repositories\BusinessStageRepository;
use Illuminate\Database\Eloquent\Model;

class BusinessStageService extends BasePersistableService
{
    private LogTrailEntityType $logTrailEntityType = LogTrailEntityType::BUSINESS_STAGE;

    public function __construct(
        private readonly BusinessStageRepository $repository,
        private readonly LogTrailService         $logTrailService,
    )
    {
    }

    public function create(
        int     $createdBy,
        string  $name,
        ?string $code,
        string  $desc
    ): BusinessStage|Model
    {
        return $this->repository->create(
            createdBy: $createdBy,
            name: $name,
            code: $code,
            desc: $desc
        );
    }


    public function update(
        string  $id,
        int     $updatedBy,
        string  $name,
        ?string $code,
        string  $desc
    ): BusinessStage|Model
    {
        $stage = $this->repository->update(
            id: $id,
            name: $name,
            code: $code,
            desc: $desc
        );

        $this->logTrailService->create(
            userId: $updatedBy,
            entityId: $id,
            entityType: $this->logTrailEntityType,
            action: LogTrailActionType::UPDATE,
            desc: 'Business stage updated',
            data: $stage,
        );

        return $stage;
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}