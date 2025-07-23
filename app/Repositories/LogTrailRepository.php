<?php

namespace App\Repositories;

use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Models\LogTrail;
use App\QueryBuilders\BaseQueryBuilder;
use App\QueryBuilders\LogTrailQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class LogTrailRepository extends BaseRepository
{
    public function __construct(
        public readonly LogTrailQueryBuilder $queryBuilder,
    ) {
    }

    public function create(
        int                 $userId,
        string              $entityId,
        LogTrailEntityType  $entityType,
        LogTrailActionType  $action,
        string              $ipAddress,
        string              $desc,
        array               $data,
        ?string             $reason = null,
        ?LogTrailEntityType $entitySubType = null,
    ): Model|LogTrail {
        return LogTrail::query()->create([
            'ip_address' => $ipAddress,
            'user_id' => $userId,
            'entity_id' => $entityId,
            'old_data' => json_encode($data),
            'action' => $action->lowercase(),
            'entity_type' => $entityType->lowercase(),
            'entity_sub_type' => $entitySubType?->lowercase(),
            'reason' => $reason,
            'desc' => $desc,
        ]);
    }

    public function queryBuilder(): BaseQueryBuilder
    {
        return $this->queryBuilder;
    }
}
