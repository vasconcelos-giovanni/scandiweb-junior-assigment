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
        // Normalize route - remove trailing slash except for root
        $route = $route === '/' ? '/' : rtrim($route, '/');
        
        $this->routes[$method][$route] = [
            'action' => $action,
            'params' => []
        ];
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
        // Normalize route - remove trailing slash except for root
        $route = $requestUri === '/' ? '/' : rtrim($requestUri, '/');
        
        // Check if the route exists for the given method
        if (!isset($this->routes[$requestMethod][$route])) {
            throw new NotFoundException("Route not found: {$requestMethod} {$route}");
        }
        
        $routeInfo = $this->routes[$requestMethod][$route];
        $action = $routeInfo['action'];
        
        // If it's a callable, execute it directly
        if (is_callable($action)) {
            return $action();
        }
        
        // If it's an array, treat it as [Controller, method]
        if (is_array($action)) {
            [$controller, $method] = $action;
            
            // Check if controller class exists
            if (!class_exists($controller)) {
                throw new NotFoundException("Controller not found: {$controller}");
            }
            
            // Instantiate the controller
            $controllerInstance = new $controller();
            
            // Check if method exists
            if (!method_exists($controllerInstance, $method)) {
                throw new NotFoundException("Method not found: {$method} in {$controller}");
            }
            
            // Call the method
            return call_user_func([$controllerInstance, $method]);
        }
        
        throw new NotFoundException("Invalid route action for: {$requestMethod} {$route}");
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