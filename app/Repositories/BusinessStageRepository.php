<?php

namespace App\Repositories;

use App\Models\BusinessStage;
use App\QueryBuilders\BusinessStageQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class BusinessStageRepository extends BaseRepository
{
    public function __construct(
        private readonly BusinessStageQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        int     $createdBy,
        string  $name,
        ?string $code,
        string  $desc
    ): BusinessStage|Model
    {
        return BusinessStage::query()->create([
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
    ): BusinessStage|Model
    {
        $stage = $this->findRequiredById($id);
        $stage->update([
            'name' => $name,
            'code' => $code,
            'description' => $desc,
        ]);

        return $stage;
    }

    public function queryBuilder(): BusinessStageQueryBuilder
    {
        return $this->queryBuilder;
    }
}