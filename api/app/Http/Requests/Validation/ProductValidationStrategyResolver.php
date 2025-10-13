<?php
declare(strict_types=1);

namespace App\Http\Requests\Validation;

use App\Core\Container;

class ProductValidationStrategyResolver
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function resolve(string $type): ProductValidationStrategyInterface
    {
        $key = "product.validation.{$type}";
        
        if (!$this->container->has($key)) {
            throw new \InvalidArgumentException("No validation strategy registered for type: {$type}");
        }
        
        return $this->container->make($key);
    }
}