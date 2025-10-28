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

    // Replace the entire handleRequest method in App.php with this new version

    public function handleRequest(): void
    {
        $request = $this->container->make(Request::class);

        $pipeline = new MiddlewarePipeline();

        foreach ($this->globalMiddleware as $middleware) {
            if (!$middleware instanceof ResponseEmitterMiddleware) {
                $pipeline->through($middleware);
            }
        }

        $pipeline->send(function () use ($request) {
            $routeAction = $this->router->resolve($request->getMethod(), $request->getUri());

            // --- CHANGE FOR CLOSURES ---
            if (is_callable($routeAction) && !is_array($routeAction)) {
                // Instead of just calling it, let our new method resolve its dependencies
                return $this->resolveDependenciesAndCall($routeAction);
            }

            // --- CHANGE FOR CONTROLLERS ---
            if (is_array($routeAction)) {
                [$controller, $method] = $routeAction;

                if (!class_exists($controller)) {
                    throw new \App\Exceptions\NotFoundException("Controller not found: {$controller}");
                }

                // Use the container to create the controller instance
                // This allows the controller's constructor to have dependencies too!
                $controllerInstance = $this->container->make($controller);

                if (!method_exists($controllerInstance, $method)) {
                    throw new \App\Exceptions\NotFoundException("Method not found: {$method} in {$controller}");
                }

                // Pass the controller instance and method to our resolver
                return $this->resolveDependenciesAndCall([$controllerInstance, $method]);
            }

            throw new \App\Exceptions\NotFoundException("Invalid route action");
        });

        $response = $pipeline->then();

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

    // Add this new private method inside your App\Core\App class

/**
 * Resolve dependencies for a callable and execute it.
 *
 * @param callable $callable
 * @return mixed
 * @throws \ReflectionException
 */
    private function resolveDependenciesAndCall(callable $callable)
    {
        // 1. Get a reflection object based on the type of callable
        $reflector = is_array($callable)
        ? new \ReflectionMethod($callable[0], $callable[1])
        : new \ReflectionFunction($callable);

        // 2. Get the parameters of the function/method
        $parameters = $reflector->getParameters();

        $dependencies = [];
        foreach ($parameters as $parameter) {
            // 3. Get the type-hinted class name for the parameter
            $type = $parameter->getType();

            // 4. If it's a valid class/interface, resolve it from the container
            if ($type && !$type->isBuiltin()) {
                $className = $type->getName();
                if ($this->container->has($className)) {
                    $dependencies[] = $this->container->make($className);
                }
            }
        }

        // 5. Call the original function/method with the resolved dependencies
        return call_user_func_array($callable, $dependencies);
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
