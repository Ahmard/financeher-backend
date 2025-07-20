<?php

namespace App\Services;

use App\Models\UserSession;
use App\Repositories\BaseRepository;
use App\Repositories\UserSessionRepository;
use Illuminate\Database\Eloquent\Model;

class UserSessionService extends BasePersistableService
{
    public function __construct(
        private readonly UserSessionRepository $repository,
    )
    {
    }

    public function create(int $userId, string $jti): UserSession|Model
    {
        return $this->repository->create(
            userId: $userId,
            jti: $jti,
        );
    }

    public function logoutOtherDevices(int $userId): void
    {
        $this->repository->logoutOtherDevices(
            userId: $userId,
        );
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}