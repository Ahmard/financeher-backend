<?php

namespace App\Services;

use App\Models\LoanVcCountry;
use App\Repositories\BaseRepository;
use App\Repositories\LoanVcCountryRepository;
use Illuminate\Database\Eloquent\Model;

class LoanVcCountryService extends BasePersistableService
{
    public function __construct(
        private readonly LoanVcCountryRepository $repository
    )
    {
    }

    public function create(
        int    $createdBy,
        string $loanVcId,
        string $countryId
    ): LoanVcCountry|Model
    {
        return $this->repository->create(
            createdBy: $createdBy,
            loanVcId: $loanVcId,
            countryId: $countryId,
        );
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}