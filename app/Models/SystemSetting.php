<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSetting extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public $casts = [
        'created_by' => 'integer',

        'moniepoint_vat' => 'integer',
        'moniepoint_card_charges' => 'integer',
        'moniepoint_transfer_charges' => 'integer',

        'system_status' => 'boolean',
        'login_module_status' => 'boolean',
        'register_module_status' => 'boolean',
        'payment_module_status' => 'boolean',
        'wallet_module_status' => 'boolean',
    ];

    protected string $modelTitle = 'system setting';
}
