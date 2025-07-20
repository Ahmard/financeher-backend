<?php

namespace App\Services;

use App\Models\GeoLocalGov;
use App\Repositories\BaseRepository;
use App\Repositories\GeoLocalGovRepository;
use Illuminate\Database\Eloquent\Model;

class GeoLocalGovService extends BasePersistableService
{
    public function __construct(
        private readonly GeoLocalGovRepository $repository,
    )
    {
    }


    public function create(
        string  $countryId,
        string  $stateId,
        string  $name,
        ?string $code,
    ): GeoLocalGov|Model
    {
        return $this->repository->create(
            countryId: $countryId,
            stateId: $stateId,
            name: $name,
            code: $code,
        );
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}