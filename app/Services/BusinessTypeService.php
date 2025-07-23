<?php

namespace App\Services;

use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Models\BusinessType;
use App\Repositories\BaseRepository;
use App\Repositories\BusinessTypeRepository;
use Illuminate\Database\Eloquent\Model;

class BusinessTypeService extends BasePersistableService
{
    private LogTrailEntityType $logTrailEntityType = LogTrailEntityType::BUSINESS_TYPE;

    public function __construct(
        private readonly BusinessTypeRepository $repository,
        private readonly LogTrailService        $logTrailService,
    ) {
    }

    public function create(
        int     $createdBy,
        string  $name,
        ?string $code,
        string  $desc
    ): BusinessType|Model {
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
    ): BusinessType|Model {
        $type = $this->repository->update(
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
            desc: 'Business type updated',
            data: $type,
        );

        return $type;
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}
