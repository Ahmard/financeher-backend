<?php

namespace App\Services;

use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Models\BaseModel;
use App\Repositories\LogTrailRepository;
use Illuminate\Database\Eloquent\Model;

class LogTrailService extends BaseService
{
    public function __construct(
        public readonly LogTrailRepository $repository,
    )
    {
    }

    public function create(
        int                   $userId,
        string|int            $entityId,
        LogTrailEntityType    $entityType,
        LogTrailActionType    $action,
        string                $desc,
        array|BaseModel|Model $data = [],
        ?string               $reason = null,
        ?LogTrailEntityType   $entitySubType = null,
    ): void
    {
        if ($data instanceof BaseModel) {
            if (!$data->isReallyUpdated() && $action != LogTrailActionType::CREATE) {
                return;
            }

            $data = $data->getChangedData();
        } elseif ($data instanceof Model) {
            $data = $data->getDirty();
        }

        $this->repository->create(
            userId: $userId,
            entityId: $entityId,
            entityType: $entityType,
            action: $action,
            ipAddress: strval(request()->ip()),
            desc: $desc,
            data: $data,
            reason: $reason,
            entitySubType: $entitySubType,
        );
    }
}
