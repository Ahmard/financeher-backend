<?php

namespace App\Exceptions;

use HttpStatusCodes\StatusCode;

class WarningException extends ResponseException
{
    public function __construct(
        string     $message,
        StatusCode $status = StatusCode::BAD_REQUEST
    )
    {
        parent::__construct(message: $message, statusCode: $status);
    }
}
