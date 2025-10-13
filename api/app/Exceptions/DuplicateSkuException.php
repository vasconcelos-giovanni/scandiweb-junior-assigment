<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Core\HttpStatus;

class DuplicateSkuException extends \Exception
{
    public function __construct(string $message = "SKU already exists", int $code = HttpStatus::CONFLICT)
    {
        parent::__construct($message, $code);
    }
}