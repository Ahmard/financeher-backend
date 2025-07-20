<?php

namespace App\QueryBuilders;

use App\Models\UserOpportunityType;
use Illuminate\Database\Eloquent\Builder;

class UserOpportunityTypeQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return UserOpportunityType::query();
    }
}
