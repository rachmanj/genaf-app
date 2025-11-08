<?php

namespace App\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class ArkFleetException extends RuntimeException
{
    public static function missingConfiguration(string $key): self
    {
        return new self("ArkFleet integration missing configuration key: {$key}");
    }

    public static function apiError(string $message, ?Response $response = null): self
    {
        if ($response === null) {
            return new self($message);
        }

        $status = $response->status();
        $decoded = $response->json();
        $body = json_encode($decoded);

        if ($body === false) {
            $body = 'unable to encode response body';
        }

        return new self("{$message} (status: {$status}, response: {$body})");
    }
}

