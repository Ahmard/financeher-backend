<?php

namespace App\Repositories\Traits;

use App\Exceptions\ModelNotFoundException;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

trait UuidRepositoryTrait
{
    /**
     * @param string $uuid
     * @return Model|BaseModel
     * @throws ModelNotFoundException
     */
    public function findRequiredByUuid(string $uuid): Model|BaseModel
    {
        $model = $this->findByUuid($uuid);

        if (null == $model) {
            $this->throwNotFoundException();
        }

        return $model;
    }

    public function findByUuid(string $uuid): Model|BaseModel|null
    {
        /** @phpstan-ignore-next-line  */
        $builder = $this->getQueryBuilder()->filterByUuid($uuid);
        return $this->getEloquentBuilder($builder)->first();
    }
}
