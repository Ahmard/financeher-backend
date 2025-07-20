<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class LogTrail extends BaseModel
{
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'log trail';

    public $searchable = [
        'columns' => [
            'log_trails.desc' => 10,
            'log_trails.reason' => 10,
            'log_trails.entity_type' => 9,
            'log_trails.entity_sub_type' => 9,
            'log_trails.action' => 8,
        ]
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
