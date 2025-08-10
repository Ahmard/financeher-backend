<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Opportunity extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'opportunity';

    public $searchable = [
        'columns' => [
            'opportunities.name' => 10,
            'opportunities.overview' => 9,
            'opportunities.application_url' => 8,
            'opportunities.amount' => 7,
        ]
    ];
}
