<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public static function routeNotFound(string $route = ''): self
    {
        $message = $route ? "Route not found: {$route}" : "Route not found";
        return new self($message, 404);
    }
}
