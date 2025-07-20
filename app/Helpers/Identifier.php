<?php

namespace App\Helpers;

use Random\RandomException;

class Identifier
{
    /**
     * Generate a unique identifier (32 chars long)
     *
     * @throws RandomException
     */
    public static function generateUniqueId(): string
    {
        return bin2hex(random_bytes(16));
    }
}