<?php

namespace App\QueryBuilders;

use App\Models\GeoCountry;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class GeoCountryQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected function builder(): Builder
    {
        return GeoCountry::query();
    }
}