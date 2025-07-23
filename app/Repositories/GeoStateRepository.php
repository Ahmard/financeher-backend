<?php

namespace App\Repositories;

use App\Models\GeoState;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\GeoStateQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class GeoStateRepository extends BaseRepository
{
    public function __construct(
        private readonly GeoStateQueryBuilder $queryBuilder,
    ) {
    }

    public function create(string $countryId, string $name, string $code): GeoState|Model
    {
        return GeoState::query()->create([
            'country_id' => $countryId,
            'name' => $name,
            'code' => $code,
        ]);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}
