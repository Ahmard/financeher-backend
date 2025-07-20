<?php

namespace App\Repositories;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Exceptions\ModelNotFoundException;
use App\Models\BaseModel;
use App\QueryBuilders\BaseQueryBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected array $columns;
    protected int $limit;
    protected int|string $ownerId;
    protected string $ownerColumn = 'user_id';

    protected array $relationship;
    private DBCompatibleEnumInterface $status;

    /**
     * @var DBCompatibleEnumInterface[]
     */
    private array $anyStatus;

    /**
     * @var DBCompatibleEnumInterface[]
     */
    private array $statusNot;

    /**
     * @return static
     * @throws BindingResolutionException
     */
    public static function new(): static
    {
        return app()->make(static::class);
    }

    public function withRelationship(array $relationship): static
    {
        $inst = clone $this;
        $inst->relationship = $relationship;
        return $inst;
    }

    /**
     * @return Collection<int, Model|BaseModel>
     */
    public function all(): Collection
    {
        return $this->getEloquentBuilder()->get();
    }

    protected function getEloquentBuilder(?Builder $builder = null): Builder
    {
        $builder ??= $this->getQueryBuilder()->all();

        if (isset($this->relationship)) {
            $builder->with($this->relationship);
        }

        return $builder;
    }

    protected function getQueryBuilder(): BaseQueryBuilder
    {
        $queryBuilder = $this->queryBuilder()->clone();

        if (isset($this->limit)) {
            $queryBuilder->withLimit($this->limit);
        }

        if (isset($this->columns)) {
            $queryBuilder->withSelect($this->columns);
        }

        if (isset($this->ownerId)) {
            $queryBuilder->withOwnerId($this->ownerId, $this->ownerColumn);
        }

        if (isset($this->status)) {
            $queryBuilder->withStatus($this->status);
        }

        if (isset($this->anyStatus)) {
            $queryBuilder->withAnyStatus($this->anyStatus);
        }

        if (isset($this->statusNot)) {
            $queryBuilder->withStatusNot($this->statusNot);
        }

        return $queryBuilder;
    }

    public function clone(): static
    {
        return clone $this;
    }

    abstract public function queryBuilder(): BaseQueryBuilder;

    public function withLimit(int $limit): static
    {
        $inst = clone $this;
        $inst->limit = $limit;
        return $inst;
    }

    public function withSelect(array $columns): static
    {
        $inst = clone $this;
        $inst->columns = $columns;
        return $inst;
    }

    public function withOwnerId(int|string $ownerId, string $column = 'user_id'): static
    {
        $inst = clone $this;
        $inst->ownerId = $ownerId;
        $inst->ownerColumn = $column;
        return $inst;
    }

    public function withStatus(DBCompatibleEnumInterface $status): static
    {
        $inst = clone $this;
        $inst->status = $status;
        return $inst;
    }

    /**
     * @param DBCompatibleEnumInterface[] $statuses
     * @return $this
     */
    public function withAnyStatus(array $statuses): static
    {
        $inst = clone $this;
        $inst->anyStatus = $statuses;
        return $inst;
    }

    /**
     * @param DBCompatibleEnumInterface[] $statuses
     * @return $this
     */
    public function withStatusNot(array $statuses): static
    {
        $inst = clone $this;
        $inst->statusNot = $statuses;
        return $inst;
    }

    public function exists(int|string $id): bool
    {
        $builder = $this->getQueryBuilder()->filterById($id);
        $existing = $this->queryBuilder()->getModel();

        $pk = sprintf('%s.%s', $existing->getTable(), $existing->getPrimaryKey());
        return $this->getEloquentBuilder($builder)
            ->select($pk)
            ->exists();
    }

    public function mustExists(int|string $id): void
    {
        $builder = $this->getQueryBuilder()->filterById($id);
        $model = $this->queryBuilder()->getModel();

        $pk = sprintf('%s.%s', $model->getTable(), $model->getPrimaryKey());
        $model = $this->getEloquentBuilder($builder)
            ->select($pk)
            ->exists();

        if (!$model) {
            $this->throwNotFoundException();
        }
    }

    /**
     * @return never
     * @throws ModelNotFoundException
     */
    protected function throwNotFoundException(): never
    {
        $notFoundMessage = $this->queryBuilder()->getModel()->getNotFoundMessage();
        throw new ModelNotFoundException($notFoundMessage);
    }

    public function deleteById(int|string $id): Model|BaseModel
    {
        $model = $this->findRequiredById($id);
        $model->delete();
        return $model;
    }

    public function findRequiredById(int|string $id): Model|BaseModel
    {
        $model = $this->findById($id);

        if (null == $model) {
            $this->throwNotFoundException();
        }

        return $model;
    }

    public function findById(int|string $id): Model|BaseModel|null
    {
        $builder = $this->getQueryBuilder()->filterById($id);
        return $this->getEloquentBuilder($builder)->first();
    }
}
