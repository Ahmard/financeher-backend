<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Nicolaslopezj\Searchable\SearchableTrait;

class UserIndustry extends BaseModel
{
    use HasUuids;
    use SearchableTrait;

    protected array $searchable = [
        'columns' => [
            'industries.name' => 10,
            'industries.id' => 9,
            'industries.code' => 9,
            'industries.description' => 8,
        ],
    ];
}
