<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * @property User $user
 */
class OpportunityType extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'opportunity type';

    protected $hidden = [
        'deleted_at',
    ];

    protected array $searchable = [
        'columns' => [
            'opportunity_types.name' => 10,
            'opportunity_types.id' => 9,
            'opportunity_types.code' => 9,
            'opportunity_types.description' => 8,
        ],
    ];
}
