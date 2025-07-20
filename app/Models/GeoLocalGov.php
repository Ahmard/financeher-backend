<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class GeoLocalGov extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    public $searchable = [
        'columns' => [
            'geo_local_govs.name' => 10,
            'geo_local_govs.code' => 9,
        ]
    ];
}
