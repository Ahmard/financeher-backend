<?php

namespace App\Services\Traits;

use App\Enums\Types\LogTrailActionType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

trait EntityDeletionTrait
{
    public function delete(string|int $id, int $deletedBy, ?string $reason = null): Model|BaseModel
    {
        $model = $this->repository->deleteById($id);
        $this->logTrailService->create(
            userId: $deletedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::DELETE,
            desc: sprintf('deleted %s', $model->getModelTitle()),
            reason: $reason
        );

        return $model;
    }
}
