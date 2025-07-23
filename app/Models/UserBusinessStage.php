<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Nicolaslopezj\Searchable\SearchableTrait;

class UserBusinessStage extends BaseModel
{
    use HasUuids;
    use SearchableTrait;

    protected array $searchable = [
        'columns' => [
            'business_stages.name' => 10,
            'business_stages.id' => 9,
            'business_stages.code' => 9,
            'business_stages.description' => 8,
        ],
    ];
}
