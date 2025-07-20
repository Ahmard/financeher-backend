<?php

namespace App\Repositories;

use App\Models\GeoCountry;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\GeoCountryQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class GeoCountryRepository extends BaseRepository
{
    public function __construct(
        private readonly GeoCountryQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        string $name,
        string $code,
        string $capital,
        string $region,
        string $subregion,
    ): GeoCountry|Model
    {
        return GeoCountry::query()->create([
            'name' => $name,
            'code' => $code,
            'capital' => $capital,
            'region' => $region,
            'subregion' => $subregion,
        ]);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}