<?php

namespace App\QueryBuilders;

use App\Models\GeoLocalGov;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class GeoLocalGovQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    public function filterByCountry(int $id): Builder
    {
        return $this
            ->all()
            ->where('geo_local_govs.country_id', $id);
    }

    public function filterByState(int $id): Builder
    {
        return $this
            ->all()
            ->where('geo_local_govs.state_id', $id);
    }

    protected function builder(): Builder
    {
        return GeoLocalGov::query();
    }
}