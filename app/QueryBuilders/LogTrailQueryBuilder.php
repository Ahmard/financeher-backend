<?php

namespace App\QueryBuilders;

use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Helpers\Http\TableColumnFilter;
use App\Models\LogTrail;
use App\Models\User;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class LogTrailQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    protected LogTrailActionType $actionType;
    protected LogTrailEntityType $entityType;
    protected LogTrailEntityType $subPawnType;

    public function withAction(LogTrailActionType $actionType): static
    {
        $this->actionType = $actionType;
        return $this;
    }

    public function withPawnType(LogTrailEntityType $entityType): static
    {
        $this->entityType = $entityType;
        return $this;
    }

    public function withSubPawnType(LogTrailEntityType $subPawnType): static
    {
        $this->subPawnType = $subPawnType;
        return $this;
    }

    public function filterItemLogs(string $itemId): Builder
    {
        return $this->all()->where('log_trails.entity_id', $itemId);
    }

    public function all(): Builder
    {
        $builder = parent::all();

        if (isset($this->entityType)) {
            $builder->where('log_trails.entity_type', $this->entityType->lowercase());
        }

        if (isset($this->subPawnType)) {
            $builder->where('log_trails.entity_sub_type', $this->subPawnType->lowercase());
        }

        return $builder;
    }

    public function datatableColumnFilter(): TableColumnFilter
    {
        return TableColumnFilter::new()
            ->add(
                column: 'full_name',
                query: User::getDatatableFilterFullNameColumn(),
                binding: fn(string $keyword) => [["%$keyword%"]],
            );
    }

    protected function builder(): Builder
    {
        return LogTrail::query()
            ->select(['log_trails.*', $this->useFullName()])
            ->join('users', 'users.id', 'log_trails.user_id');
    }
}
