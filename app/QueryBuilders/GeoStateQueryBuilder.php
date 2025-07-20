<?php

namespace App\QueryBuilders;

use App\Models\GeoState;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class GeoStateQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    public function filterByCountry(int $id): Builder
    {
        return $this
            ->all()
            ->where('geo_states.country_id', $id);
    }

    protected function builder(): Builder
    {
        return GeoState::query();
    }
}