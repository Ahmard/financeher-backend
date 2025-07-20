<?php

namespace App\QueryBuilders;

use App\Enums\UserRole;
use App\Models\User;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class UserQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected UserRole $role;

    public function all(): Builder
    {
        $builder = parent::all();

        if (isset($this->role)) {
            $builder->join('model_has_roles', 'model_id', 'users.id')
                ->join('roles', 'role_id', 'roles.id')
                ->where('roles.name', $this->role->lowercase());
        }

        return $builder;
    }

    public function findByEmail(string $email): Builder
    {
        return $this->all()->where('users.email', $email);
    }

    public function findByMobileNumber(string $umber): Builder
    {
        return $this->all()->where('users.mobile_number', $umber);
    }

    public function filterAdminTeamMembers(): Builder
    {
        return $this
            ->all()
            ->where('users.is_admin_team_member', true);
    }

    public function admins(): Builder
    {
        return $this->withRole(UserRole::ADMIN)->all();
    }

    public function withRole(UserRole $role): static
    {
        $this->role = $role;
        return $this;
    }

    protected function builder(): Builder
    {
        return User::query();
    }
}
