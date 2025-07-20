<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property User $user
 */
class BusinessType extends BaseModel
{
    use HasUuids;
    use SoftDeletes;

    protected string $modelTitle = 'business type';

    protected $hidden = [
        'deleted_at',
    ];
}
