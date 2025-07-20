<?php

namespace App\Helpers;

class Image
{
    public static function toBase64(string $absPath): string
    {
        return base64_encode(file_get_contents($absPath));
    }
}