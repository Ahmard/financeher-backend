<?php

namespace App\Services;

use App\Enums\Entity;
use App\Enums\Types\FileUploadOwnerType;
use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Exceptions\WarningException;
use App\Helpers\Http\Uploader\Uploader;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\FileUpload;
use App\Repositories\FileUploadRepository;
use Illuminate\Database\Eloquent\Model;

class FileUploadService extends BaseService
{
    public function __construct(
        public readonly FileUploadRepository $repository,
        protected readonly LogTrailService   $logTrailService,
    ) {
    }

    /**
     * @param int $userId
     * @param Entity $entity
     * @param string|int $ownerId
     * @param string $fieldName
     * @param string|null $desc
     * @param string|null $additionalInfo
     * @return array<int, Model|FileUpload>
     * @throws WarningException
     */
    public function upload(
        int        $userId,
        Entity     $entity,
        string|int $ownerId,
        string     $fieldName,
        ?string    $desc = null,
        ?string    $additionalInfo = null,
    ): array {
        $files = Uploader::upload($fieldName);
        $uploads = [];

        foreach ($files as $file) {
            $uploads[] = $this->create(
                userId: $userId,
                entity: $entity,
                ownerId: $ownerId,
                origName: $file->getOriginalName(),
                fileRelativePath: $file->getRelativePath(),
                desc: $desc,
                additionalInfo: $additionalInfo
            );
        }

        return $uploads;
    }

    /**
     * @param array<int, Model|FileUpload> $data
     * @return string
     * @throws WarningException
     */
    public static function collectFirstPath(array $data): string
    {
        if (empty($data)) {
            throw new WarningException('No files were uploaded');
        }

        return $data[0]['file_path'];
    }

    /**
     * @param int $userId Current logged user ID
     * @param Entity $entity
     * @param string|int $ownerId
     * @param string $origName
     * @param string $fileRelativePath
     * @param string|null $desc
     * @param string|null $additionalInfo
     * @return Model|FileUpload
     */
    public function create(
        int        $userId,
        Entity     $entity,
        string|int $ownerId,
        string     $origName,
        string     $fileRelativePath,
        ?string    $desc = null,
        ?string    $additionalInfo = null,
    ): Model|FileUpload {
        $expFilePath = explode('.', $fileRelativePath);
        $ext = end($expFilePath);

        return $this->repository->create(
            userId: $userId,
            entity: $entity,
            entityId: $ownerId,
            origName: $origName,
            fileRelativePath: $fileRelativePath,
            fileExt: $ext,
            desc: $desc,
            additionalInfo: $additionalInfo
        );
    }

    public function delete(
        int    $deletedBy,
        int    $id,
        Entity $entity,
        int    $ownerId
    ): void {
        $this->repository->delete(
            entity: $entity,
            ownerId: $ownerId,
            id: $id
        );

        $this->logTrailService->create(
            userId: $deletedBy,
            entityId: $ownerId,
            entityType: LogTrailEntityType::UPLOADED_FILE,
            action: LogTrailActionType::DELETE,
            desc: '',
        );
    }
}
