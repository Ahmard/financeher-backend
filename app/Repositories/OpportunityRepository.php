<?php

namespace App\Repositories;

use App\Helpers\Carbon;
use App\Models\Opportunity;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\OpportunityQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class OpportunityRepository extends BaseRepository
{
    public function __construct(
        private readonly OpportunityQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        int    $createdBy,
        string $businessTypeId,
        string $opportunityTypeId,
        string $name,
        float  $lowerAmount,
        float  $upperAmount,
        string $logo,
        string $overview,
        string $applicationUrl,
        string $closingAt,
    ): Opportunity|Model
    {
        return Opportunity::query()->create([
            'created_by' => $createdBy,
            'business_type_id' => $businessTypeId,
            'opportunity_type_id' => $opportunityTypeId,
            'name' => $name,
            'lower_amount' => $lowerAmount,
            'upper_amount' => $upperAmount,
            'logo' => $logo,
            'overview' => $overview,
            'application_url' => $applicationUrl,
            'closing_at' => $closingAt,
        ]);
    }

    public function update(
        Opportunity|Model $opportunity,
        int               $updatedBy,
        string            $businessTypeId,
        string            $opportunityTypeId,
        string            $name,
        float             $lowerAmount,
        float             $upperAmount,
        string            $logo,
        string            $overview,
        string            $applicationUrl,
        Carbon            $closingAt,
    ): Model|Opportunity
    {
        $opportunity->update([
            'updated_by' => $updatedBy,
            'business_type_id' => $businessTypeId,
            'opportunity_type_id' => $opportunityTypeId,
            'name' => $name,
            'lower_amount' => $lowerAmount,
            'upper_amount' => $upperAmount,
            'logo' => $logo,
            'overview' => $overview,
            'application_url' => $applicationUrl,
            'closing_at' => $closingAt,
        ]);

        return $opportunity;
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}