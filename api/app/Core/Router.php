<?php
declare(strict_types=1);

namespace App\Core;

use App\Exceptions\NotFoundException;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->routes = [
            'GET' => [],
            'POST' => [],
            'DELETE' => []
        ];
    }

    /**
     * Register a GET route.
     *
     * @param string $route
     * @param array|callable $action
     * @return self
     */
    public function get(string $route, $action): self
    {
        $this->registerRoute('GET', $route, $action);
        return $this;
    }

    /**
     * Register a POST route.
     *
     * @param string $route
     * @param array|callable $action
     * @return self
     */
    public function post(string $route, $action): self
    {
        $this->registerRoute('POST', $route, $action);
        return $this;
    }

    /**
     * Register a DELETE route.
     *
     * @param string $route
     * @param array|callable $action
     * @return self
     */
    public function delete(string $route, $action): self
    {
        $this->registerRoute('DELETE', $route, $action);
        return $this;
    }

    /**
     * Register a route with the given method.
     *
     * @param string $method
     * @param string $route
     * @param array|callable $action
     */
    private function registerRoute(string $method, string $route, $action): void
    {
        // Normalize route - add leading slash if missing and remove trailing slash
        $normalizedRoute = $this->normalizeRoute($route);
        
        $this->routes[$method][$normalizedRoute] = [
            'action' => $action,
            'params' => []
        ];
    }

    /**
     * Normalize a route URI.
     *
     * @param string $route
     * @return string
     */
    private function normalizeRoute(string $route): string
    {
        // Add leading slash if missing
        if (empty($route)) {
            return '/';
        }
        
        if (strpos($route, '/') !== 0) {
            $route = '/' . $route;
        }
        
        // Remove trailing slash except for root
        return $route === '/' ? '/' : rtrim($route, '/');
    }

    /**
     * Resolve the current request.
     *
     * @param string $requestMethod
     * @param string $requestUri
     * @return mixed
     * @throws NotFoundException
     */
    public function resolve(string $requestMethod, string $requestUri)
    {
        // Normalize the request URI
        $normalizedUri = $this->normalizeRoute($requestUri);
        
        // Check if the route exists for the given method
        if (!isset($this->routes[$requestMethod][$normalizedUri])) {
            throw new NotFoundException("Route not found: {$requestMethod} {$normalizedUri}");
        }
        
        $routeInfo = $this->routes[$requestMethod][$normalizedUri];
        
        // Return the action so the App class can execute it
        return $routeInfo['action'];
    }

    /**
     * Get all registered routes.
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}