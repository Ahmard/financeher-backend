<?php

namespace App\QueryBuilders;

use App\Models\UserBusinessStage;
use Illuminate\Database\Eloquent\Builder;

class UserBusinessStageQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return UserBusinessStage::query();
    }
}
