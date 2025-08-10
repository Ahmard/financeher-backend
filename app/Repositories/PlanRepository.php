<?php

namespace App\Repositories;

use App\Enums\Types\BillingCycleKind;
use App\Models\Plan;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\PlanQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class PlanRepository extends BaseRepository
{
    public function __construct(
        private readonly PlanQueryBuilder $queryBuilder,
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
        return Plan::query()->create([
            'created_by' => $createdBy,
            'name' => $name,
            'price' => $price,
            'features' => json_encode($features),
            'billing_cycle' => $billingCycle->lowercase(),
        ]);
    }

    public function update(
        string           $id,
        string           $name,
        float            $price,
        array            $features,
        BillingCycleKind $billingCycle,
    ): Plan|Model
    {
        $plan = $this->findRequiredById($id);
        $plan->update([
            'name' => $name,
            'price' => $price,
            'features' => $features,
            'billing_cycle' => $billingCycle->lowercase(),
        ]);

        return $plan;
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}