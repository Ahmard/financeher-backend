<?php

namespace App\Helpers\Http\Uploader;

use App\Exceptions\WarningException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class Uploader
{
    /**
     * @param string|UploadedFile $fieldName
     * @param string $path
     * @return UploadSuccess[]
     * @throws WarningException
     */
    public static function document(string|UploadedFile $fieldName, string $path = '/'): array
    {
        return self::upload($fieldName, $path);
    }

    /**
     * @param string|UploadedFile $fieldName
     * @param string $path
     * @return UploadSuccess[]
     * @throws WarningException
     */
    public static function upload(string|UploadedFile $fieldName, string $path = '/'): array
    {
        $file = $fieldName;

        $upload = function ($file) use ($path, $fieldName) {
            if ($file instanceof UploadedFile) {
                $newName = "uploads$path" . md5(strval(microtime(true))) . ".{$file->getClientOriginalExtension()}";
                $theLocation = $newName;
                if (str_contains(Request::url(), 'localhost') || str_contains(Request::url(), '127.0.0.1')) {
                    $theLocation = public_path($newName);
                }

                move_uploaded_file($file->getRealPath(), public_path($newName));

                return new UploadSuccess(
                    relativePath: $newName,
                    theLocation: $theLocation,
                    originalName: $file->getClientOriginalName()
                );
            }

            Log::debug($file);

            throw new WarningException(sprintf('Failed to upload file: %s', $fieldName));
        };

        if (is_string($fieldName)) {
            $file = request()->file($fieldName);
        }

        if (!is_array($file)) {
            $file = [$file];
        }

        $uploaded = [];
        foreach ($file as $fileItem) {
            $uploaded[] = $upload($fileItem);
        }

        return $uploaded;
    }

    /**
     * @param string $fieldName
     * @param string $path
     * @return UploadSuccess[]
     * @throws WarningException
     */
    public static function image(string $fieldName, string $path = '/'): array
    {
        return self::upload($fieldName, $path);
    }
}
