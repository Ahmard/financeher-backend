<?php

namespace App\Exceptions;

use HttpStatusCodes\StatusCode;

class NotImplementedException extends ResponseException
{
    public function __construct(
        string     $message,
        StatusCode $status = StatusCode::NOT_IMPLEMENTED
    )
    {
        parent::__construct(message: $message, statusCode: $status);
    }
}
