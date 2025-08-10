<?php

namespace App\QueryBuilders;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;

class PlanQueryBuilder extends BaseQueryBuilder
{

    protected function builder(): Builder
    {
        return Plan::withCreatorJoin();
    }
}
