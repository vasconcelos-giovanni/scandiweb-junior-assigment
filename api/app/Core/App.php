<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Config;
use App\Core\Container;
use App\Core\Database;
use App\Core\DB;
use App\Http\Middlewares\SystemRoutesMiddleware;
use App\Core\Router;
use App\Core\Request;
use App\Providers\ServiceProvider;
use App\Http\Middlewares\ResponseEmitterMiddleware;
use App\Core\MiddlewarePipeline;
use App\Http\Middlewares\ExceptionHandlerMiddleware;

class App
{
    private Container $container;
    private Router $router;
    private array $serviceProviders = [];
    private bool $booted = false;
    private array $globalMiddleware = [];

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router();
        $this->registerCoreServices();
        $this->registerGlobalMiddleware();
    }

    /**
     * Register core framework services.
     */
    private function registerCoreServices(): void
    {
        // Bind the app instance
        $this->container->instance('app', $this);
        
        // Register Request
        $this->container->singleton(Request::class, function () {
            return new Request();
        });
        
        // Register Config
        $this->container->singleton(Config::class, function () {
            return new Config(dirname(__DIR__, 2) . '/.env');
        });
        
        // Register Database
        $this->container->singleton(Database::class, function ($container) {
            return new Database($container->make(Config::class));
        });
        
        // Set the database for DB facade
        $this->container->resolving(Database::class, function ($database) {
            DB::setDatabase($database);
        });
        
        // Register Router
        $this->container->singleton(Router::class, function () {
            return $this->router;
        });
    }

    /**
     * Register a service provider.
     *
     * @param ServiceProvider $provider
     * @return void
     */
    public function register(ServiceProvider $provider): void
    {
        $provider->register();
        $this->serviceProviders[] = $provider;
    }

    /**
     * Register global middleware.
     */
    private function registerGlobalMiddleware(): void
    {
        $this->globalMiddleware = [
            new SystemRoutesMiddleware(),
            new ExceptionHandlerMiddleware(),
            new ResponseEmitterMiddleware()
        ];
    }

    /**
     * Boot the application and all service providers.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->serviceProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }

        $this->booted = true;
    }

    /**
     * Get the router instance.
     *
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Get the container instance.
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Resolve a service from the container.
     *
     * @param string $abstract
     * @return mixed
     */
    public function make(string $abstract)
    {
        return $this->container->make($abstract);
    }

    /**
     * Handle the incoming HTTP request.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        // Get the request instance
        $request = $this->container->make(Request::class);
        
        // Create middleware pipeline
        $pipeline = new MiddlewarePipeline();
        
        // Add global middleware (without ResponseEmitterMiddleware)
        foreach ($this->globalMiddleware as $middleware) {
            if (!$middleware instanceof ResponseEmitterMiddleware) {
                $pipeline->through($middleware);
            }
        }
        
        // Set the destination (route handler) - this closure will resolve the route
        $pipeline->send(function () use ($request) {
            // Resolve the route
            $routeAction = $this->router->resolve($request->getMethod(), $request->getUri());
            
            // If it's a callable (closure), execute it directly
            if (is_callable($routeAction)) {
                return $routeAction();
            }
            
            // If it's an array, treat it as [Controller, method]
            if (is_array($routeAction)) {
                [$controller, $method] = $routeAction;
                
                // Check if controller class exists
                if (!class_exists($controller)) {
                    throw new \App\Exceptions\NotFoundException("Controller not found: {$controller}");
                }
                
                // Instantiate the controller
                $controllerInstance = new $controller();
                
                // Check if method exists
                if (!method_exists($controllerInstance, $method)) {
                    throw new \App\Exceptions\NotFoundException("Method not found: {$method} in {$controller}");
                }
                
                // Call the method
                return call_user_func([$controllerInstance, $method]);
            }
            
            // If it's a string, treat it as a function name
            if (is_string($routeAction) && function_exists($routeAction)) {
                return $routeAction();
            }
            
            throw new \App\Exceptions\NotFoundException("Invalid route action");
        });
        
        // Execute the pipeline
        $response = $pipeline->then();
        
        // Handle the response
        $this->sendResponse($response);
    }

    /**
     * Send the response to the browser.
     *
     * @param mixed $response
     * @return void
     */
    private function sendResponse($response): void
    {
        if ($response instanceof \App\Core\Response) {
            // Set HTTP status code
            http_response_code($response->getStatus());
            
            // Set headers
            foreach ($response->getHeaders() as $key => $value) {
                header("$key: $value");
            }
            
            // Output JSON encoded data
            echo json_encode($response->getData());
        } elseif (is_array($response)) {
            // Legacy support for plain arrays
            header('Content-Type: application/json');
            echo json_encode($response);
        } elseif (is_string($response)) {
            echo $response;
        }
    }

    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void
    {
        $this->boot();
        $this->handleRequest();
    }
}