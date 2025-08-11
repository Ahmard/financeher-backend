<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Plan extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'plan';

    public $searchable = [
        'columns' => [
            'plans.name' => 10,
            'plans.billing_cycle' => 8,
            'plans.price' => 7,
        ]
    ];

    public $casts = [
        'features' => 'array',
    ];

    public function intoMiscData(): array
    {
        return [
            'name' => $this['name'],
            'billing_cycle' => $this['billing_cycle'],
            'price' => $this['price'],
            'features' => $this['features'],
        ];
    }
}
