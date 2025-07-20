<?php

namespace App\Services;

use App\Models\GeoCountry;
use App\Repositories\BaseRepository;
use App\Repositories\GeoCountryRepository;
use Illuminate\Database\Eloquent\Model;

class GeoCountryService extends BasePersistableService
{
    public function __construct(
        private readonly GeoCountryRepository $repository,
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
        return $this->repository->create(
            name: $name,
            code: $code,
            capital: $capital,
            region: $region,
            subregion: $subregion,
        );
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}