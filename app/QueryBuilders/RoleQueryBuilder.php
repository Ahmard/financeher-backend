<?php

namespace App\QueryBuilders;

use App\QueryBuilders\BaseQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class RoleQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return Role::query();
    }
}
