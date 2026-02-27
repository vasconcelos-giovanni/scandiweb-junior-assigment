<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $server;
    private array $get;
    private array $post;
    private array $headers;

    public function __construct(
        array $server = null,
        array $get = null,
        array $post = null,
        array $headers = null
    ) {
        $this->server = $server ?? $_SERVER;
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->headers = $headers ?? $this->getHeadersFromServer();
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';

        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        return $uri;
    }

    /**
     * Get a specific header value.
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function getHeader(string $key, ?string $default = null): ?string
    {
        $normalizedKey = strtoupper(str_replace('-', '_', $key));
        return $this->headers[$normalizedKey] ?? $default;
    }

    /**
     * Get all headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get a query parameter.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getQuery(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get all query parameters.
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->get;
    }

    /**
     * Get a POST parameter.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPost(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get all POST parameters.
     *
     * @return array
     */
    public function getPostParams(): array
    {
        return $this->post;
    }

    /**
     * Get the request body as JSON.
     *
     * @return array|null
     */
    public function getJson(): ?array
    {
        $rawBody = file_get_contents('php://input');

        if (empty($rawBody)) {
            return null;
        }

        $contentType = $this->getHeader('Content-Type', '');

        // Parse if Content-Type is JSON, or if the body looks like JSON as fallback
        if (
            strpos($contentType, 'application/json') !== false
            || $this->looksLikeJson($rawBody)
        ) {
            $decoded = json_decode($rawBody, true);
            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    /**
     * Check if a string looks like JSON (starts with { or [).
     *
     * @param string $str
     * @return bool
     */
    private function looksLikeJson(string $str): bool
    {
        $trimmed = ltrim($str);
        return isset($trimmed[0]) && ($trimmed[0] === '{' || $trimmed[0] === '[');
    }

    /**
     * Extract headers from $_SERVER.
     *
     * @return array
     */
    private function getHeadersFromServer(): array
    {
        $headers = [];

        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerKey = substr($key, 5);
                $headers[$headerKey] = $value;
            }
        }

        // Add some common headers that might not have HTTP_ prefix
        $specialHeaders = [
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'CONTENT_MD5',
        ];

        foreach ($specialHeaders as $serverKey) {
            if (isset($this->server[$serverKey]) && !isset($headers[$serverKey])) {
                $headers[$serverKey] = $this->server[$serverKey];
            }
        }

        return $headers;
    }
}
