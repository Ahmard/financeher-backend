<?php

namespace App\Services\Traits;

use App\Enums\Statuses\BinaryStatus;
use App\Enums\Types\LogTrailActionType;
use Illuminate\Database\Eloquent\Model;

trait StatusManipulatorTrait
{
    use EntityDeletionTrait;

    /**
     * @param int|string $id
     * @param int $activatedBy
     * @param string $reason
     * @param int|null $ownerId
     * @param array $additionalFields Additional fields to update while activating
     * @return Model
     */
    public function activate(
        int|string $id,
        int        $activatedBy,
        string     $reason,
        ?int       $ownerId = null,
        array      $additionalFields = [],
    ): Model {
        $model = match (isset($ownerId)) {
            true => $this->repository->withOwnerId($ownerId)->findRequiredById($id),
            default => $this->repository->findRequiredById($id),
        };

        $model->update([
            'status' => BinaryStatus::ACTIVE->lowercase(),
            ...$additionalFields,
        ]);

        $this->logTrailService->create(
            userId: $activatedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::UPDATE,
            desc: sprintf('activated %s', $model->getModelTitle()),
            data: $model,
            reason: $reason
        );

        return $model;
    }

    /**
     * @param int|string $id
     * @param int $deactivatedBy
     * @param string $reason
     * @param int|null $ownerId
     * @param array $additionalFields Additional fields to update while deactivating
     * @return Model
     */
    public function deactivate(
        int|string $id,
        int        $deactivatedBy,
        string     $reason,
        ?int       $ownerId = null,
        array      $additionalFields = [],
    ): Model {
        $model = match (isset($ownerId)) {
            true => $this->repository->withOwnerId($ownerId)->findRequiredById($id),
            default => $this->repository->findRequiredById($id),
        };

        $model->update([
            'status' => BinaryStatus::INACTIVE->lowercase(),
            ...$additionalFields,
        ]);

        $this->logTrailService->create(
            userId: $deactivatedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::UPDATE,
            desc: sprintf('deactivated %s', $model->getModelTitle()),
            data: $model,
            reason: $reason
        );

        return $model;
    }
}
