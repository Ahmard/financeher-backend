<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * @property User $user
 */
class BusinessType extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'business type';

    protected $hidden = [
        'deleted_at',
    ];


    protected array $searchable = [
        'columns' => [
            'business_types.name' => 10,
            'business_types.id' => 9,
            'business_types.code' => 9,
            'business_types.description' => 8,
        ],
    ];
}
