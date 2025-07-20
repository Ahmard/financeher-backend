<?php

namespace App\QueryBuilders;

use App\Models\ModelHasRole;
use Illuminate\Database\Eloquent\Builder;

class ModelHasRoleQueryBuilder extends BaseQueryBuilder
{
    public function filterByUser(int $id): Builder
    {
        return $this
            ->all()
            ->where('model_has_roles.model_id', $id);
    }

    public function filterAssignable(int $userId): Builder
    {
        return RoleQueryBuilder::new()
            ->all()
            ->whereNotIn('roles.id', function (\Illuminate\Database\Query\Builder $builder) use ($userId) {
                $builder
                    ->select('model_has_roles.role_id')
                    ->from('model_has_roles')
                    ->where('model_has_roles.model_id', $userId);
            });
    }

    protected function builder(): Builder
    {
        return ModelHasRole::query()
            ->select(['model_has_roles.*', 'roles.name'])
            ->join('roles', 'roles.id', 'model_has_roles.role_id');
    }
}
