<?php

namespace App\Exceptions;

use HttpStatusCodes\StatusCode;
use InvalidArgumentException;

class NotFoundException extends ResponseException
{
    public function __construct(string $message)
    {
        parent::__construct(message: $message, statusCode: StatusCode::NOT_FOUND);
    }
}
