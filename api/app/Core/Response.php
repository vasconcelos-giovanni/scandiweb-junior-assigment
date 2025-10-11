<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    private mixed $data;
    private int $status;
    private array $headers;

    public function __construct(
        mixed $data = null,
        int $status = 200,
        array $headers = []
    ) {
        $this->data = $data;
        $this->status = $status;
        $this->headers = array_merge(['Content-Type' => 'application/json'], $headers);
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Create a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return static
     */
    public static function json(
        mixed $data = null,
        int $status = 200,
        array $headers = []
    ): self {
        return new self($data, $status, $headers);
    }

    /**
     * Create a success JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return static
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200
    ): self {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Create an error JSON response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @return static
     */
    public static function error(
        string $message = 'Error',
        mixed $data = null,
        int $status = 400
    ): self {
        return new self([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Create a validation error response.
     *
     * @param array $errors
     * @param string $message
     * @return static
     */
    public static function validationError(
        array $errors,
        string $message = 'Validation failed'
    ): self {
        return new self([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }

    /**
     * Create a not found response.
     *
     * @param string $message
     * @return static
     */
    public static function notFound(string $message = 'Resource not found'): self
    {
        return new self([
            'success' => false,
            'message' => $message
        ], 404);
    }

    /**
     * Create a created response.
     *
     * @param mixed $data
     * @param string $message
     * @return static
     */
    public static function created(
        mixed $data = null,
        string $message = 'Resource created successfully'
    ): self {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 201);
    }
}