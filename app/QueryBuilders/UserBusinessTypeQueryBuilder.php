<?php

namespace App\QueryBuilders;

use App\Models\UserBusinessType;
use Illuminate\Database\Eloquent\Builder;

class UserBusinessTypeQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return UserBusinessType::query();
    }
}
