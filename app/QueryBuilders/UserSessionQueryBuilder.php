<?php

namespace App\QueryBuilders;

use App\Models\UserSession;
use App\QueryBuilders\Traits\UuidQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class UserSessionQueryBuilder extends BaseQueryBuilder
{
    use UuidQueryBuilderTrait;

    protected function builder(): Builder
    {
        return UserSession::query();
    }
}