<?php
// declare(strict_types=1);

// namespace App\Providers;

// use App\Core\Container;
// use App\Infrastructure\Factories\DvdProductFactory;
// use App\Infrastructure\Factories\BookProductFactory;
// use App\Infrastructure\Factories\FurnitureProductFactory;
// use App\Infrastructure\Factories\ProductFactoryResolver;
// use App\Infrastructure\Hydrators\DvdProductHydrator;
// use App\Infrastructure\Hydrators\BookProductHydrator;
// use App\Infrastructure\Hydrators\FurnitureProductHydrator;
// use App\Infrastructure\Hydrators\ProductHydratorRegistry;
// use App\Repositories\ProductRepository;

// class ProductServiceProvider extends ServiceProvider
// {
//     public function register(): void
//     {
//         // Register factories
//         $this->app->singleton('product.factory.dvd', function () {
//             return new DvdProductFactory();
//         });
        
//         $this->app->singleton('product.factory.book', function () {
//             return new BookProductFactory();
//         });
        
//         $this->app->singleton('product.factory.furniture', function () {
//             return new FurnitureProductFactory();
//         });
        
//         // Register factory resolver
//         $this->app->singleton(ProductFactoryResolver::class, function ($app) {
//             $resolver = new ProductFactoryResolver($app);
//             return $resolver;
//         });
        
//         // Register hydrators
//         $this->app->singleton('product.hydrator.dvd', function () {
//             return new DvdProductHydrator();
//         });
        
//         $this->app->singleton('product.hydrator.book', function () {
//             return new BookProductHydrator();
//         });
        
//         $this->app->singleton('product.hydrator.furniture', function () {
//             return new FurnitureProductHydrator();
//         });
        
//         // Register hydrator registry
//         $this->app->singleton(ProductHydratorRegistry::class, function ($app) {
//             $registry = new ProductHydratorRegistry();
//             $registry->register('dvd', $app->make('product.hydrator.dvd'));
//             $registry->register('book', $app->make('product.hydrator.book'));
//             $registry->register('furniture', $app->make('product.hydrator.furniture'));
//             return $registry;
//         });
        
//         // Register repository
//         $this->app->singleton(ProductRepository::class, function ($app) {
//             return new ProductRepository(
//                 $app->make('db'),
//                 $app->make(ProductHydratorRegistry::class)
//             );
//         });
//     }
// }