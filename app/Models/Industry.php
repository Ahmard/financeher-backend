<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * @property User $user
 */
class Industry extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'industry';

    protected $hidden = [
        'deleted_at',
    ];


    protected array $searchable = [
        'columns' => [
            'industries.name' => 10,
            'industries.id' => 9,
            'industries.code' => 9,
            'industries.description' => 8,
        ],
    ];
}
