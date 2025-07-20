<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class GeoCountry extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    public $searchable = [
        'columns' => [
            'geo_countries.name' => 10,
            'geo_countries.code' => 9,
        ]
    ];
}
