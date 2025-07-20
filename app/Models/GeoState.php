<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class GeoState extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    public $searchable = [
        'columns' => [
            'geo_states.name' => 10,
            'geo_states.code' => 9,
        ]
    ];
}
