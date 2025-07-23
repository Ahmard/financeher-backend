<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * @property User $user
 */
class BusinessStage extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'business stage';

    protected $hidden = [
        'deleted_at',
    ];


    protected array $searchable = [
        'columns' => [
            'business_stages.name' => 10,
            'business_stages.id' => 9,
            'business_stages.code' => 9,
            'business_stages.description' => 8,
        ],
    ];
}
