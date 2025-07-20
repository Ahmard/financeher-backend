<?php

namespace App\QueryBuilders;

use App\Models\OpportunityType;
use Illuminate\Database\Eloquent\Builder;

class OpportunityTypeQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return OpportunityType::query();
    }
}
