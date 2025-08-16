<?php

namespace App\QueryBuilders;

use App\Models\Industry;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class IndustryQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected function builder(): Builder
    {
        return Industry::query();
    }
}
