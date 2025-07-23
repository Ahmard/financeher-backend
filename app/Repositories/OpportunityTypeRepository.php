<?php

namespace App\Repositories;

use App\Models\BusinessType;
use App\Models\OpportunityType;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\BusinessTypeQueryBuilder;
use App\QueryBuilders\OpportunityTypeQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class OpportunityTypeRepository extends BaseRepository
{
    public function __construct(
        private readonly OpportunityTypeQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        int     $createdBy,
        string  $name,
        ?string $code,
        string  $desc
    ): OpportunityType|Model {
        return OpportunityType::query()->create([
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
    ): OpportunityType|Model {
        $type = $this->findRequiredById($id);
        $type->update([
            'name' => $name,
            'code' => $code,
            'description' => $desc,
        ]);

        return $type;
    }

    public function queryBuilder(): OpportunityTypeQueryBuilder
    {
        return $this->queryBuilder;
    }
}
