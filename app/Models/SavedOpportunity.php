<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class SavedOpportunity extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'saved opportunity';


    public $searchable = [
        'columns' => [
            'opportunities.name' => 10,
            'opportunities.overview' => 9,
            'opportunities.application_url' => 8,
            'opportunities.lower_amount' => 7,
            'opportunities.upper_amount' => 7,
        ],
    ];
}
