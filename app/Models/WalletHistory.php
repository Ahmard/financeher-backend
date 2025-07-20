<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletHistory extends BaseModel
{
    use HasUuids;
    use SoftDeletes;

    protected string $modelTitle = 'wallet history';
}
