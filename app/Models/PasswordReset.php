<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = "password_reset_tokens";
    protected string $modelTitle = "password resets";
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
