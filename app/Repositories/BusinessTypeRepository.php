<?php

namespace App\Repositories;

use App\Models\BusinessType;
use App\QueryBuilders\BusinessTypeQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class BusinessTypeRepository extends BaseRepository
{
    public function __construct(
        private readonly BusinessTypeQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        int     $createdBy,
        string  $name,
        ?string $code,
        string  $desc
    ): BusinessType|Model {
        return BusinessType::query()->create([
            'created_by' => $createdBy,
            'name' => $name,
            'code' => $code,
            'description' => $desc,
        ]);
    }

    public function update(
        string  $id,
        string  $name,
        ?string $code,
        string  $desc
    ): BusinessType|Model {
        $type = $this->findRequiredById($id);
        $type->update([
            'name' => $name,
            'code' => $code,
            'description' => $desc,
        ]);

        return $type;
    }

    public function queryBuilder(): BusinessTypeQueryBuilder
    {
        return $this->queryBuilder;
    }
}
