<?php
declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Core\MiddlewareInterface;

class SystemRoutesMiddleware implements MiddlewareInterface
{
    public function handle(\Closure $next)
    {
        // Get the request URI
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Patterns to match system routes
        $systemPatterns = [
            '/favicon.ico',
            '/.well-known/*',  // Matches all .well-known routes
            '/robots.txt',
            '/sitemap.xml'
        ];
        
        // Check if the request URI matches any system pattern
        foreach ($systemPatterns as $pattern) {
            if ($this->matchesPattern($requestUri, $pattern)) {
                http_response_code(204);
                return '';
            }
        }
        
        // Otherwise, continue with the next middleware
        return $next();
    }
    
    /**
     * Check if a URI matches a pattern.
     *
     * @param string $uri
     * @param string $pattern
     * @return bool
     */
    private function matchesPattern(string $uri, string $pattern): bool
    {
        // Exact match
        if ($pattern === $uri) {
            return true;
        }
        
        // Wildcard match (e.g., "/.well-known/*") (PHP 7.4 compatible)
        if (strpos($pattern, '*') !== false) {
            $prefix = rtrim($pattern, '*');
            // Use strpos for prefix check, which is equivalent to str_starts_with
            return strpos($uri, $prefix) === 0;
        }
        
        return false;
    }
}