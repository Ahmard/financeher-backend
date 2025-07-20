<?php

namespace App\Services;

use App\Enums\Types\LogTrailEntityType;
use App\QueryBuilders\BaseQueryBuilder;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Http\Message\ResponseInterface;

abstract class BasePersistableService
{
    protected LogTrailEntityType $logTrailPawnType = LogTrailEntityType::MULTIPURPOSE_USE;

    /**
     * @return static
     * @throws BindingResolutionException
     */
    public static function new(): BasePersistableService
    {
        return app()->make(static::class);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->repository()->queryBuilder();
    }

    public function decodeResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true) ?? [];
    }

    abstract public function repository(): BaseRepository;
}
