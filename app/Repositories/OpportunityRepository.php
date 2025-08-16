<?php

namespace App\Repositories;

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
        string $countryId,
        string $industryId,
        string $opportunityTypeId,
        string $name,
        string $organisation,
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
            'country_id' => $countryId,
            'industry_id' => $industryId,
            'opportunity_type_id' => $opportunityTypeId,
            'name' => $name,
            'organisation' => $organisation,
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
        string            $countryId,
        string            $businessTypeId,
        string            $opportunityTypeId,
        string            $name,
        string            $organisation,
        float             $lowerAmount,
        float             $upperAmount,
        string            $logo,
        string            $overview,
        string            $applicationUrl,
        string            $closingAt,
    ): Model|Opportunity
    {
        $opportunity->update([
            'country_id' => $countryId,
            'business_type_id' => $businessTypeId,
            'opportunity_type_id' => $opportunityTypeId,
            'name' => $name,
            'organisation' => $organisation,
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
