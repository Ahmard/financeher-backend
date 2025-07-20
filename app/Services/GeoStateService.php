<?php

namespace App\Services;

use App\Models\GeoState;
use App\Repositories\BaseRepository;
use App\Repositories\GeoStateRepository;
use Illuminate\Database\Eloquent\Model;

class GeoStateService extends BasePersistableService
{
    public function __construct(
        private readonly GeoStateRepository $repository,
    )
    {
    }

    public function create(string $countryId, string $name, string $code): GeoState|Model
    {
        return $this->repository->create(
            countryId: $countryId,
            name: $name,
            code: $code,
        );
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}