<?php

namespace App\QueryBuilders;

use App\Models\BusinessType;
use Illuminate\Database\Eloquent\Builder;

class BusinessTypeQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return BusinessType::query();
    }
}
