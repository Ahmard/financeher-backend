<?php

namespace App\QueryBuilders;

use App\Models\Permission;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class PermissionQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected function builder(): Builder
    {
        return Permission::query();
    }

    public function filterRolePermissions(int $roleId): Builder
    {
        return $this->all()
            ->select(['permissions.id', 'permissions.name'])
            ->join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', '=', $roleId);
    }

    public function filterRoleAssignable(int $roleId): Builder
    {
        return $this->all()
            ->select(['permissions.id', 'permissions.name'])
            ->whereNotIn('permissions.id', function (\Illuminate\Database\Query\Builder $query) use ($roleId) {
                $query->select('permission_id')
                    ->from('role_has_permissions')
                    ->where('role_id', $roleId);
            });
    }
}
