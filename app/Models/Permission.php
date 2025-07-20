<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Permission extends Model
{
    use SearchableTrait;

    protected array $searchable = [
        'columns' => [
            'permissions.name' => 10,
        ],
    ];
}
