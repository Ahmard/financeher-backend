<?php

namespace App\Services;

use App\Exceptions\WarningException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Http\Message\ResponseInterface;

abstract class BaseService
{
    /**
     * @return static
     * @throws BindingResolutionException
     */
    public static function new(): static
    {
        return app()->make(static::class);
    }

    /**
     * @param ClientException $exception
     * @return never
     * @throws WarningException
     */
    public function handleClientException(ClientException $exception): never
    {
        $resp = json_decode($exception->getResponse()->getBody()->getContents(), true);
        throw new WarningException($resp['message'] ?? $resp['error']);
    }

    public function decodeResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true) ?? [];
    }
}
