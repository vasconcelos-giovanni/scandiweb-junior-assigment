<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Response;

class ResponseEmitterMiddleware implements MiddlewareInterface
{
    public function handle(\Closure $next)
    {
        // Execute the next middleware/route handler
        $response = $next();

        // Debug: Log what we received
        error_log('Response type: ' . gettype($response));
        if (is_object($response)) {
            error_log('Response class: ' . get_class($response));
        }

        // Handle the response
        if ($response instanceof Response) {
            // Debug: Log that we found a Response object
            error_log('Found Response object');

            // Set HTTP status code
            http_response_code($response->getStatus());

            // Set headers
            foreach ($response->getHeaders() as $key => $value) {
                header("$key: $value");
                error_log("Setting header: $key: $value");
            }

            // Output JSON encoded data
            $jsonData = json_encode($response->getData());
            error_log('JSON output: ' . $jsonData);
            echo $jsonData;
        } elseif (is_array($response)) {
            // Legacy support for plain arrays
            header('Content-Type: application/json');
            echo json_encode($response);
        } elseif (is_string($response)) {
            echo $response;
        } else {
            error_log('Unexpected response type: ' . gettype($response));
        }

        return $response;
    }
}
