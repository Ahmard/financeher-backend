<?php

namespace App\QueryBuilders;

use App\Models\BusinessType;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class BusinessTypeQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected function builder(): Builder
    {
        return BusinessType::query();
    }
}
