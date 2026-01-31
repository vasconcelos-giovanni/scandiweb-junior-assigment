<?php

declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Core\Container;

/**
 * Resolves the appropriate ProductFactory based on product type.
 *
 * Uses the Container to lookup registered factory instances,
 * avoiding conditional statements for type handling as required
 * by Scandiweb guidelines.
 */
class ProductFactoryResolver
{
    /** @var Container */
    private Container $container;

    /**
     * Create a new ProductFactoryResolver instance.
     *
     * @param Container $container The DI container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve the appropriate factory for a product type.
     *
     * @param string $type The product type (e.g., 'dvd', 'book', 'furniture').
     * @return ProductFactory The factory instance.
     * @throws \InvalidArgumentException If no factory is registered for the type.
     */
    public function resolve(string $type): ProductFactory
    {
        $key = "product.factory.{$type}";

        if (!$this->container->has($key)) {
            throw new \InvalidArgumentException(
                "No factory registered for product type: {$type}"
            );
        }

        return $this->container->make($key);
    }

    /**
     * Check if a factory exists for the given product type.
     *
     * @param string $type The product type.
     * @return bool True if a factory is registered.
     */
    public function hasFactory(string $type): bool
    {
        $key = "product.factory.{$type}";
        return $this->container->has($key);
    }
}
