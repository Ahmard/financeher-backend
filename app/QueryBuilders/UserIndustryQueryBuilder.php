<?php

namespace App\QueryBuilders;

use App\Models\UserIndustry;
use Illuminate\Database\Eloquent\Builder;

class UserIndustryQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return UserIndustry::query();
    }
}
