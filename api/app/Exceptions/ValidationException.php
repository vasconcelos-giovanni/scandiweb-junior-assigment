<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Core\HttpStatus;

class ValidationException extends \Exception
{
    protected array $errors;

    public function __construct(
        array $errors,
        string $message = 'Validation failed',
        int $code = HttpStatus::UNPROCESSABLE_ENTITY
    ) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
