<?php

namespace App\QueryBuilders;

use App\Models\LoanVcCountry;
use Illuminate\Database\Eloquent\Builder;

class LoanVcCountryQueryBuilder extends BaseQueryBuilder
{

    protected function builder(): Builder
    {
        return LoanVcCountry::withCreatorJoin()
            ->join('geo_countries', 'geo_countries.id', 'loan_vc_countries.id');
    }
}