<?php

namespace App\QueryBuilders;

use App\Models\OpportunityType;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class OpportunityTypeQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected function builder(): Builder
    {
        return OpportunityType::query();
    }
}
