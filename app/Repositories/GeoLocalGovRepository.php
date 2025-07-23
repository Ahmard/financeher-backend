<?php

namespace App\Repositories;

use App\Models\GeoLocalGov;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\GeoLocalGovQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class GeoLocalGovRepository extends BaseRepository
{
    public function __construct(
        private readonly GeoLocalGovQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        string  $countryId,
        string  $stateId,
        string  $name,
        ?string $code,
    ): GeoLocalGov|Model {
        return GeoLocalGov::query()->create([
            'country_id' => $countryId,
            'state_id' => $stateId,
            'name' => $name,
            'code' => $code,
        ]);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}
