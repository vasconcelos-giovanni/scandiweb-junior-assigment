<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Config;
use App\Core\Container;
use App\Core\Database;
use App\Core\DB;
use App\Core\Router;
use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Providers\ServiceProvider;

class App
{
    private Container $container;
    private Router $router;
    private array $serviceProviders = [];
    private bool $booted = false;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router();
        $this->registerCoreServices();
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
        
        try {
            // Resolve the route
            $response = $this->router->resolve($request->getMethod(), $request->getUri());
            
            // Send the response
            if (is_string($response)) {
                echo $response;
            } elseif (is_array($response)) {
                header('Content-Type: application/json');
                echo json_encode($response);
            }
        } catch (NotFoundException $e) {
            http_response_code(404);
            echo $e->getMessage();
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Internal Server Error: ' . $e->getMessage();
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