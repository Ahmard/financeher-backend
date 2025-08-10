<?php

namespace App\Repositories;

use App\Models\LoanVcCountry;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\LoanVcQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class LoanVcCountryRepository extends BaseRepository
{
    public function __construct(
        private readonly LoanVcQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        int    $createdBy,
        string $loanVcId,
        string $countryId
    ): LoanVcCountry|Model
    {
        return LoanVcCountry::query()->create([
            'created_by' => $createdBy,
            'loan_vc_id' => $loanVcId,
            'country_id' => $countryId,
        ]);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}