<?php

namespace App\QueryBuilders;

use App\Enums\Entity;
use App\Enums\Type\FileUploadOwnerType;
use App\Models\FileUpload;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class FileUploadQueryBuilder extends BaseQueryBuilder
{
    protected Entity $entity;

    public function withOwnerType(Entity $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function all(): Builder
    {
        $builder = parent::all();

        if (isset($this->entity)) {
            $builder->where('file_uploads.entity_type', $this->entity->lowercase());
        }

        return $builder;
    }

    protected function builder(): Builder
    {
        return FileUpload::withCreatorJoin(pkColumn: 'user_id', fieldName: 'full_name')
            ->select(['file_uploads.*'])
            ->join('users', 'users.id', 'file_uploads.user_id');
    }
}
