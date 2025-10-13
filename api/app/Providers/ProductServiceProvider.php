<?php
declare(strict_types=1);

namespace App\Providers;

use App\Core\Container;
use App\Core\Database;
use App\Http\Controllers\ProductController;
use App\Http\Requests\Validation\BookProductValidationStrategy;
use App\Http\Requests\Validation\DvdProductValidationStrategy;
use App\Http\Requests\Validation\FurnitureProductValidationStrategy;
use App\Http\Requests\Validation\ProductValidationStrategyResolver;
use App\Infrastructure\Factories\DvdProductFactory;
use App\Infrastructure\Factories\BookProductFactory;
use App\Infrastructure\Factories\FurnitureProductFactory;
use App\Infrastructure\Factories\ProductFactoryResolver;
use App\Infrastructure\Hydrators\DvdProductHydrator;
use App\Infrastructure\Hydrators\BookProductHydrator;
use App\Infrastructure\Hydrators\FurnitureProductHydrator;
use App\Infrastructure\Hydrators\ProductHydratorRegistry;
use App\Repositories\ProductRepository;
use App\Services\ProductService;

class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // === FACTORIES ===
        // We bind each factory to a unique key so the resolver can find them.
        $this->app->singleton('product.factory.dvd', fn() => new DvdProductFactory());
        $this->app->singleton('product.factory.book', fn() => new BookProductFactory());
        $this->app->singleton('product.factory.furniture', fn() => new FurnitureProductFactory());

        // === FACTORY RESOLVER ===
        // This class needs the container itself to resolve the factories above.
        $this->app->singleton(ProductFactoryResolver::class, fn(Container $app) => new ProductFactoryResolver($app));

        // === HYDRATORS ===
        // We bind each hydrator to a unique key for the registry.
        $this->app->singleton('product.hydrator.dvd', fn() => new DvdProductHydrator());
        $this->app->singleton('product.hydrator.book', fn() => new BookProductHydrator());
        $this->app->singleton('product.hydrator.furniture', fn() => new FurnitureProductHydrator());
        
        // === HYDRATOR REGISTRY ===
        // We create a single registry and then populate it with the hydrators we just bound.
        $this->app->singleton(ProductHydratorRegistry::class, function (Container $app) {
            $registry = new ProductHydratorRegistry();
            $registry->register('dvd', $app->make('product.hydrator.dvd'));
            $registry->register('book', $app->make('product.hydrator.book'));
            $registry->register('furniture', $app->make('product.hydrator.furniture'));
            return $registry;
        });

        // === REPOSITORY ===
        // The repository depends on the Database connection and the Hydrator Registry.
        $this->app->singleton(ProductRepository::class, function (Container $app) {
            return new ProductRepository(
                $app->make(Database::class),
                $app->make(ProductHydratorRegistry::class)
            );
        });

        // === SERVICE ===
        // The service is the main entry point for our controllers. It depends on the Repository and the Factory Resolver.
        $this->app->singleton(ProductService::class, function (Container $app) {
            return new ProductService(
                $app->make(ProductRepository::class),
                $app->make(ProductFactoryResolver::class)
            );
        });

        // === VALIDATION STRATEGIES ===
        $this->app->singleton('product.validation.dvd', fn() => new DvdProductValidationStrategy());
        $this->app->singleton('product.validation.book', fn() => new BookProductValidationStrategy());
        $this->app->singleton('product.validation.furniture', fn() => new FurnitureProductValidationStrategy());

        // === VALIDATION RESOLVER ===
        $this->app->singleton(ProductValidationStrategyResolver::class, fn(Container $app) => new ProductValidationStrategyResolver($app));

        // Register product controller
        $this->app->singleton(ProductController::class, function ($app) {
            return new ProductController($app->make(ProductService::class));
        });
    }
}