<?php
declare(strict_types=1);

namespace App\Providers;

use App\Core\Router;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Nothing to register here
    }

    public function boot(): void
    {
        // Get the router from the container
        $router = $this->app->make(Router::class);
        
        // Load the routes file
        require_once dirname(__DIR__, 2) . '/routes/api.php';
    }
}