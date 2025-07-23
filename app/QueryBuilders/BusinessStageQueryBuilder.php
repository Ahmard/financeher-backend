<?php

namespace App\QueryBuilders;

use App\Models\BusinessStage;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class BusinessStageQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected function builder(): Builder
    {
        return BusinessStage::query();
    }
}
