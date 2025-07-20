<?php

namespace App\QueryBuilders;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Helpers\Http\TableColumnFilter;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseQueryBuilder
{
    protected DBCompatibleEnumInterface $status;

    protected string $uuidPrimaryKeyField = 'uuid';

    /**
     * @var DBCompatibleEnumInterface[]
     */
    protected array $anyStatus;

    /**
     * @var DBCompatibleEnumInterface[]
     */
    protected array $statusNot;

    protected int $limit;
    protected array $columns;
    protected int|string $ownerId;
    protected string $ownerColumn = 'user_id';

    protected string $dateColumn;
    protected string $startDate;
    protected string $endDate;

    public function filterById(int|string $id): Builder
    {
        $builder = $this->all();
        $model = $builder->getModel();
        $table = $model->getTable();
        $primaryKey = method_exists($model, 'getPrimaryKey')
            ? $model->getPrimaryKey()
            : 'id';

        return $this->all()->where("$table.$primaryKey", $id);
    }

    public function all(): Builder
    {
        $builder = $this->builder();
        $tableName = $builder->getModel()->getTable();

        if (isset($this->searchQuery)) {
            $builder->search($this->searchQuery);
        }

        if (isset($this->status)) {
            $builder->where("$tableName.status", $this->status->lowercase());
        }

        if (isset($this->anyStatus)) {
            $builder->whereIn(
                "$tableName.status",
                array_map(fn (DBCompatibleEnumInterface $enum) => $enum->lowercase(), $this->anyStatus)
            );
        }

        if (isset($this->statusNot)) {
            $builder->whereNotIn(
                "$tableName.status",
                array_map(fn ($f) => $f->lowercase(), $this->statusNot)
            );
        }

        if (isset($this->columns)) {
            $builder->select($this->columns);
        }

        if (isset($this->limit)) {
            $builder->limit($this->limit);
        }

        if (isset($this->ownerId)) {
            $builder->where($this->ownerColumn, $this->ownerId);
        }

        if (isset($this->startDate)) {
            $builder->whereBetween("$tableName.$this->dateColumn", [$this->startDate, $this->endDate]);
        }

        return $builder;
    }

    public function allDesc(string $orderTable = 'created_at'): Builder
    {
        $builder = $this->builder();
        $tableName = $builder->getModel()->getTable();
        return $this
            ->all()
            ->orderByDesc("$tableName.$orderTable");
    }

    abstract protected function builder(): Builder;

    public function getModel(): Model|BaseModel
    {
        return $this->builder()->getModel();
    }

    public function withSelect(array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function withStatus(DBCompatibleEnumInterface $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param DBCompatibleEnumInterface[] $statuses
     * @return $this
     */
    public function withAnyStatus(array $statuses): static
    {
        $this->anyStatus = $statuses;
        return $this;
    }

    /**
     * @param DBCompatibleEnumInterface[] $statuses
     * @return $this
     */
    public function withStatusNot(array $statuses): static
    {
        $this->statusNot = $statuses;
        return $this;
    }

    public function withLimit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function withOwnerId(int|string $ownerId, string $column = 'user_id'): static
    {
        $this->ownerId = $ownerId;
        $this->ownerColumn = $column;
        return $this;
    }

    public function withDateRange(string $start, string $end, string $column = 'created_at'): static
    {
        $this->dateColumn = $column;
        $this->startDate = $start;
        $this->endDate = $end;
        return $this;
    }

    public function clone(): static
    {
        return clone $this;
    }

    public function getTableName(): string
    {
        return $this->builder()->getModel()->getTable();
    }

    public function datatableColumnFilter(): TableColumnFilter
    {
        return TableColumnFilter::new();
    }

    /**
     * @return static
     * @throws BindingResolutionException
     */
    public static function new(): static
    {
        return app()->make(static::class);
    }

    protected function useCreatorFullName(): Expression
    {
        return $this->useFullName(table: 'creator', as: 'creator_full_name');
    }

    protected function useFullName(string $table = 'users', string $as = 'full_name', ?string $prefix = null): Expression
    {
        return User::getFullNameColumn(table: $table, prefix: $prefix, as: $as);
    }
}
