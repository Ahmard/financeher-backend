<?php

namespace App\Repositories;

use App\Helpers\Carbon;
use App\Models\LoanVc;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\LoanVcQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class LoanVcRepository extends BaseRepository
{
    public function __construct(
        private readonly LoanVcQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        int    $createdBy,
        string $industryId,
        string $opportunityTypeId,
        string $organisation,
        float  $lowerAmount,
        float  $upperAmount,
        string $logo,
        string $description,
        string $applicationUrl,
        string $closingAt,
    ): LoanVc|Model
    {
        return LoanVc::query()->create([
            'created_by' => $createdBy,
            'industry_id' => $industryId,
            'opportunity_type_id' => $opportunityTypeId,
            'organisation' => $organisation,
            'lower_amount' => $lowerAmount,
            'upper_amount' => $upperAmount,
            'logo' => $logo,
            'description' => $description,
            'application_url' => $applicationUrl,
            'closing_at' => $closingAt,
        ]);
    }

    public function update(
        LoanVc|Model $lvc,
        string       $businessTypeId,
        string       $opportunityTypeId,
        string       $organisation,
        float        $lowerAmount,
        float        $upperAmount,
        string       $logo,
        string       $description,
        string       $applicationUrl,
        Carbon       $closingAt,
    ): Model|LoanVc
    {
        $lvc->update([
            'business_type_id' => $businessTypeId,
            'opportunity_type_id' => $opportunityTypeId,
            'organisation' => $organisation,
            'lower_amount' => $lowerAmount,
            'upper_amount' => $upperAmount,
            'logo' => $logo,
            'description' => $description,
            'application_url' => $applicationUrl,
            'closing_at' => $closingAt,
        ]);

        return $lvc;
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}
