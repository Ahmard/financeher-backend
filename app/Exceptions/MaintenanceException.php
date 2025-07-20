<?php

namespace App\Exceptions;

use HttpStatusCodes\StatusCode;

class MaintenanceException extends ResponseException
{
    public function __construct(string $message)
    {
        parent::__construct(message: $message, statusCode: StatusCode::SERVICE_UNAVAILABLE);
    }
}
