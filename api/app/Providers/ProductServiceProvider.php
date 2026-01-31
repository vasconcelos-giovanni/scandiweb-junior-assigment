<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Dto\CreateBookDto;
use App\Application\Dto\CreateDvdDto;
use App\Application\Dto\CreateFurnitureDto;
use App\Application\Dto\DtoResolver;
use App\Application\Services\ProductService;
use App\Core\Container;
use App\Core\Database;
use App\Infrastructure\Factories\BookProductFactory;
use App\Infrastructure\Factories\DvdProductFactory;
use App\Infrastructure\Factories\FurnitureProductFactory;
use App\Infrastructure\Factories\ProductFactoryResolver;
use App\Infrastructure\Hydrators\BookProductHydrator;
use App\Infrastructure\Hydrators\DvdProductHydrator;
use App\Infrastructure\Hydrators\FurnitureProductHydrator;
use App\Infrastructure\Hydrators\ProductHydratorRegistry;
use App\Infrastructure\Repositories\ProductRepository;

/**
 * Service Provider for Product-related dependencies.
 *
 * Registers all factories, hydrators, repositories, and services
 * needed for the Product domain.
 */
class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register all product-related bindings.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerDtos();
        $this->registerFactories();
        $this->registerHydrators();
        $this->registerRepositories();
        $this->registerServices();
    }

    /**
     * Register DTO class mappings.
     *
     * @return void
     */
    private function registerDtos(): void
    {
        // Register DTO class names by product type
        $this->app->bind('product.dto.dvd', CreateDvdDto::class);
        $this->app->bind('product.dto.book', CreateBookDto::class);
        $this->app->bind('product.dto.furniture', CreateFurnitureDto::class);

        // Register DtoResolver
        $this->app->singleton(DtoResolver::class, function (Container $c) {
            return new DtoResolver($c);
        });
    }

    /**
     * Register Product factories.
     *
     * @return void
     */
    private function registerFactories(): void
    {
        // Register individual factories
        $this->app->singleton(DvdProductFactory::class, function () {
            return new DvdProductFactory();
        });

        $this->app->singleton(BookProductFactory::class, function () {
            return new BookProductFactory();
        });

        $this->app->singleton(FurnitureProductFactory::class, function () {
            return new FurnitureProductFactory();
        });

        // Register factories by product type for the resolver
        $this->app->bind('product.factory.dvd', function (Container $c) {
            return $c->make(DvdProductFactory::class);
        });

        $this->app->bind('product.factory.book', function (Container $c) {
            return $c->make(BookProductFactory::class);
        });

        $this->app->bind('product.factory.furniture', function (Container $c) {
            return $c->make(FurnitureProductFactory::class);
        });

        // Register ProductFactoryResolver
        $this->app->singleton(ProductFactoryResolver::class, function (Container $c) {
            return new ProductFactoryResolver($c);
        });
    }

    /**
     * Register Product hydrators.
     *
     * @return void
     */
    private function registerHydrators(): void
    {
        // Register individual hydrators
        $this->app->singleton(DvdProductHydrator::class, function () {
            return new DvdProductHydrator();
        });

        $this->app->singleton(BookProductHydrator::class, function () {
            return new BookProductHydrator();
        });

        $this->app->singleton(FurnitureProductHydrator::class, function () {
            return new FurnitureProductHydrator();
        });

        // Register hydrator registry
        $this->app->singleton(ProductHydratorRegistry::class, function (Container $c) {
            $registry = new ProductHydratorRegistry();

            $registry->register('dvd', $c->make(DvdProductHydrator::class));
            $registry->register('book', $c->make(BookProductHydrator::class));
            $registry->register('furniture', $c->make(FurnitureProductHydrator::class));

            return $registry;
        });
    }

    /**
     * Register Product repositories.
     *
     * @return void
     */
    private function registerRepositories(): void
    {
        $this->app->singleton(ProductRepository::class, function (Container $c) {
            return new ProductRepository(
                $c->make(Database::class),
                $c->make(ProductHydratorRegistry::class)
            );
        });
    }

    /**
     * Register Product services.
     *
     * @return void
     */
    private function registerServices(): void
    {
        $this->app->singleton(ProductService::class, function (Container $c) {
            return new ProductService(
                $c->make(ProductRepository::class),
                $c->make(ProductFactoryResolver::class),
                $c->make(DtoResolver::class)
            );
        });
    }
}
