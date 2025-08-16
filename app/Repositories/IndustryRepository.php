<?php

namespace App\Repositories;

use App\Models\Industry;
use App\QueryBuilders\IndustryQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class IndustryRepository extends BaseRepository
{
    public function __construct(
        private readonly IndustryQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        int     $createdBy,
        string  $name,
        ?string $code,
        string  $desc
    ): Industry|Model {
        return Industry::query()->create([
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
    ): Industry|Model {
        $type = $this->findRequiredById($id);
        $type->update([
            'name' => $name,
            'code' => $code,
            'description' => $desc,
        ]);

        return $type;
    }

    public function queryBuilder(): IndustryQueryBuilder
    {
        return $this->queryBuilder;
    }
}
