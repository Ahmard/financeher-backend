<?php

namespace App\Services;

use App\Enums\Types\BillingCycleKind;
use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Models\Plan;
use App\Repositories\BaseRepository;
use App\Repositories\PlanRepository;
use App\Services\Traits\EntityDeletionTrait;
use Illuminate\Database\Eloquent\Model;

class PlanService extends BasePersistableService
{
    use EntityDeletionTrait;

    protected LogTrailEntityType $logTrailPawnType = LogTrailEntityType::PLAN;

    public function __construct(
        private readonly PlanRepository  $repository,
        private readonly LogTrailService $logTrailService,
    )
    {
    }

    public function create(
        int              $createdBy,
        string           $name,
        float            $price,
        array            $features,
        BillingCycleKind $billingCycle,
    ): Plan|Model
    {
        return $this->repository->create(
            createdBy: $createdBy,
            name: $name,
            price: $price,
            features: $features,
            billingCycle: $billingCycle,
        );
    }

    public function update(
        string           $id,
        string           $updatedBy,
        string           $name,
        float            $price,
        array            $features,
        BillingCycleKind $billingCycle,
    ): Plan|Model
    {
        $plan = $this->repository->update(
            id: $id,
            name: $name,
            price: $price,
            features: $features,
            billingCycle: $billingCycle,
        );

        $this->logTrailService->create(
            userId: $updatedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::UPDATE,
            desc: 'Plan updated',
            data: $plan,
        );

        return $plan;
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }

    public function pageMetrics(): array
    {
        return [
            'all' => Plan::query()->count(),
        ];
    }
}