<?php

namespace App\QueryBuilders;

use App\Models\BusinessStage;
use Illuminate\Database\Eloquent\Builder;

class BusinessStageQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return BusinessStage::query();
    }
}
