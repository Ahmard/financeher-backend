<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property User $user
 */
class Wallet extends BaseModel
{
    use HasUuids;
    use SoftDeletes;

    protected string $modelTitle = 'wallet';

    public $casts = [
        'balance' => 'float',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
