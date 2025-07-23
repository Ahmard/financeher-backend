<?php

namespace App\Repositories;

use App\Enums\Entity;
use App\Models\FileUpload;
use App\QueryBuilders\FileUploadQueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class FileUploadRepository extends BaseRepository
{
    protected Entity $entity;


    public function __construct(
        public readonly FileUploadQueryBuilder $queryBuilder,
    ) {
    }

    /**
     * @param int $userId Current logged user ID
     * @param Entity $entity
     * @param string|int $entityId
     * @param string $origName
     * @param string $fileRelativePath
     * @param string $fileExt
     * @param string|null $desc
     * @param string|null $additionalInfo
     * @return Model|FileUpload
     */
    public function create(
        int        $userId,
        Entity     $entity,
        string|int $entityId,
        string     $origName,
        string     $fileRelativePath,
        string     $fileExt,
        ?string    $desc = null,
        ?string    $additionalInfo = null,
    ): Model|FileUpload {
        return FileUpload::query()->create([
            'user_id' => $userId,
            'entity_type' => $entity->lowercase(),
            'entity_id' => $entityId,
            'orig_name' => $origName,
            'file_path' => $fileRelativePath,
            'file_ext' => $fileExt,
            'desc' => $desc,
            'additional_info' => $additionalInfo,
        ]);
    }

    public function delete(Entity $entity, int $ownerId, int $id): void
    {
        $this->withOwnerType($entity)
            ->withOwnerId(ownerId: $ownerId, column: 'entity_id')
            ->findRequiredById($id)
            ->delete();
    }

    public function withOwnerType(Entity $ownerType): static
    {
        $this->entity = $ownerType;
        return $this;
    }

    /**
     * @param Entity $entity
     * @param int|string $ownerId
     * @return Collection<int, Model|FileUpload>
     */
    public function getAll(Entity $entity, int|string $ownerId): Collection
    {
        return $this->queryBuilder()
            ->withOwnerType($entity)
            ->withOwnerId($ownerId, 'entity_id')
            ->all()
            ->get();
    }

    public function queryBuilder(): FileUploadQueryBuilder
    {
        $queryBuilder = $this->queryBuilder;

        if (isset($this->entity)) {
            $queryBuilder->withOwnerType($this->entity);
        }

        return $queryBuilder;
    }
}
