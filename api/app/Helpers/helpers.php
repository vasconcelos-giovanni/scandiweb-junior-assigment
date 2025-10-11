<?php
declare(strict_types=1);

if (!function_exists('jsonResponse')) {
    /**
     * Create a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return array
     */
    function jsonResponse(
        mixed $data = null,
        int $status = 200,
        array $headers = []
    ): array {
        // Set HTTP status code
        http_response_code($status);
        
        // Set default headers
        $defaultHeaders = ['Content-Type' => 'application/json'];
        $allHeaders = array_merge($defaultHeaders, $headers);
        
        // Set headers
        foreach ($allHeaders as $key => $value) {
            header("$key: $value");
        }
        
        // Return the data (will be handled by App class)
        return $data;
    }
}

if (!function_exists('json')) {
    /**
     * Alias for jsonResponse.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return array
     */
    function json(
        mixed $data = null,
        int $status = 200,
        array $headers = []
    ): array {
        return jsonResponse($data, $status, $headers);
    }
}

if (!function_exists('response')) {
    /**
     * Response helper for creating responses.
     *
     * @return \App\Core\Response
     */
    function response(): \App\Core\Response
    {
        return new \App\Core\Response();
    }
}