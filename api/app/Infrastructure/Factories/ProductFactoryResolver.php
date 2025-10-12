<?php
declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Core\Container;

class ProductFactoryResolver
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function resolve(string $type): ProductFactory
    {
        $key = "product.factory.{$type}";

        if (!$this->container->has($key)) {
            throw new \InvalidArgumentException("No factory registered for product type: {$type}");
        }

        return $this->container->make($key);
    }
}