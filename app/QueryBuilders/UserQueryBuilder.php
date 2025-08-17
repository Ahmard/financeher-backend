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
    protected bool $filterBusiness = false;

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

    public function filterBusinesses(): Builder
    {
        return $this->all()->where('users.is_admin', false);
    }

    public function filterAdmins(): Builder
    {
        return $this
            ->all()
            ->where('users.is_admin', true)
            ->selectSub(function (\Illuminate\Database\Query\Builder $builder) {
                $builder
                    ->selectRaw('GROUP_CONCAT(roles.name SEPARATOR ", ")')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereRaw('users.id = model_has_roles.model_id');
            }, 'role_names');
    }

    public function withRole(UserRole $role): static
    {
        $this->role = $role;
        return $this;
    }

    protected function builder(): Builder
    {
        return User::query()
            ->select([
                'users.*',
                'industries.name as industry_name',
                'business_stages.name as business_stage_name',
            ])
            ->leftJoin('industries', 'industries.id', 'users.industry_id')
            ->leftJoin('business_stages', 'business_stages.id', 'users.business_stage_id');
    }
}
