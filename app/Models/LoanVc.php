<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class LoanVc extends BaseModel
{
    use HasUuids;
    use SoftDeletes;
    use SearchableTrait;

    protected string $modelTitle = 'loan/vc';

    public $searchable = [
        'columns' => [
            'loan_vcs.organisation' => 10,
            'loan_vcs.description' => 9,
            'loan_vcs.application_url' => 8,
            'loan_vcs.lower_amount' => 7,
            'loan_vcs.upper_amount' => 7,
        ]
    ];
}
