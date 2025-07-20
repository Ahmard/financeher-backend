<?php

namespace App\Services;

use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Models\OpportunityType;
use App\Repositories\OpportunityTypeRepository;
use Illuminate\Database\Eloquent\Model;

class OpportunityTypeService extends BasePersistableService
{
    private LogTrailEntityType $logTrailEntityType = LogTrailEntityType::OPPORTUNITY_TYPE;

    public function __construct(
        private readonly OpportunityTypeRepository $repository,
        private readonly LogTrailService           $logTrailService,
    )
    {
    }

    public function create(
        int     $createdBy,
        string  $name,
        ?string $code,
        string  $desc
    ): OpportunityType|Model
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
    ): OpportunityType|Model
    {
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
            desc: 'Opportunity type updated',
            data: $type,
        );

        return $type;
    }

    public function repository(): OpportunityTypeRepository
    {
        return $this->repository;
    }
}