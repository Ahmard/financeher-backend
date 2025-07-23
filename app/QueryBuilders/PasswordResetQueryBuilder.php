<?php

namespace App\QueryBuilders;

use App\Models\PasswordReset;
use Illuminate\Database\Eloquent\Builder;

class PasswordResetQueryBuilder extends BaseQueryBuilder
{
    protected function builder(): Builder
    {
        return PasswordReset::query();
    }
}
