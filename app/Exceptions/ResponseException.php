<?php

namespace App\Exceptions;

use Exception;
use HttpStatusCodes\StatusCode;
use Throwable;

class ResponseException extends Exception
{
    /**
     * @param StatusCode $code
     * @param string $message
     * @return never
     * @throws ResponseException
     */
    public static function throw(string $message, StatusCode $code): never
    {
        throw new ResponseException($message, $code);
    }

    public function __construct(
        protected                   $message,
        protected                   $code = 500,
        private readonly StatusCode $statusCode = StatusCode::UNAUTHORIZED,
        ?Throwable                  $previous = null
    )
    {
        parent::__construct($message, $this->statusCode->value, $previous);
    }

    public function __toString(): string
    {
        return "{$this->statusCode->describe()->desc}: [{$this->statusCode->value}] {$this->message} in {$this->file} on line {$this->line}\n" .
            $this->getTraceAsString();
    }
}
