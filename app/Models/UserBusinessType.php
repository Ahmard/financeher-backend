<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Nicolaslopezj\Searchable\SearchableTrait;

class UserBusinessType extends BaseModel
{
    use HasUuids;
    use SearchableTrait;

    protected array $searchable = [
        'columns' => [
            'business_types.name' => 10,
            'business_types.id' => 9,
            'business_types.code' => 9,
            'business_types.description' => 8,
        ],
    ];
}
