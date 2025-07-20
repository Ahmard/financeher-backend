<?php

namespace App\Helpers\Http\Uploader;

use Illuminate\Support\Facades\Log;
use Throwable;

class UploadError
{
    public function __construct(
        protected string    $path,
        protected string    $fieldName,
        protected Throwable $error,
    ) {
        Log::critical($this->__toString());
    }

    public function __toString(): string
    {
        return strval(json_encode([
            'error' => strval($this->error),
            'fieldName' => $this->fieldName,
            'path' => $this->path
        ]));
    }

    /**
     * @return Throwable
     */
    public function getError(): Throwable
    {
        return $this->error;
    }
}
