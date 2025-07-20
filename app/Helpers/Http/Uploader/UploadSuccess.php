<?php

namespace App\Helpers\Http\Uploader;

class UploadSuccess
{
    public function __construct(
        protected string $relativePath,
        protected string $theLocation,
        protected string $originalName,
    ) {
    }

    /**
     * Gets file relative path
     *
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    /**
     * Get file absolute path
     *
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return public_path($this->relativePath);
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->theLocation;
    }
}
