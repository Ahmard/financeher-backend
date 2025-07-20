<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Nicolaslopezj\Searchable\SearchableTrait;

class Payment extends BaseModel
{
    use HasUuids;
    use SearchableTrait;

    public $casts = [
        'payer_id' => 'integer',
        'captured_by' => 'integer',
        'deleted_by' => 'integer',
        'amount' => 'float',
        'paid_amount' => 'float',
        'charges' => 'float',
        'computed_amount' => 'float',
        'is_manual_capture' => 'boolean',
        'is_direct_transfer' => 'boolean',
        'paid_at' => 'date:Y-m-d H:i:s',
    ];

    protected array $searchable = [
        'columns' => [
            'payments.status' => 10,
            'payments.reference' => 9,
            'payments.gateway_reference' => 8,
            'payments.purpose' => 7,
        ],
    ];

    protected $hidden = [
        'deleted_at',
        'init_response',
        'webhook_event',
    ];

    protected string $modelTitle = 'payment';
}
