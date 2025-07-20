<?php

namespace App\QueryBuilders\Traits;

use Illuminate\Database\Eloquent\Builder;

trait UuidQueryBuilderTrait
{

    public function filterByUuid(string $uuid): Builder
    {
        $builder = $this->all();
        $model = $builder->getModel();
        $table = $model->getTable();

        return $this->all()->where("$table.$this->uuidPrimaryKeyField", $uuid);
    }

}
