<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FileUpload extends BaseModel
{
    use SoftDeletes;

    protected string $modelTitle = 'file upload';
}
