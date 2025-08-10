<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class LoanVcCountry extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'loan/vc country';

    public $searchable = [
        'columns' => [
            'loan_vc_countries.country_id' => 10,
            'loan_vc_countries.loan_vc_id' => 9,
        ]
    ];
}
