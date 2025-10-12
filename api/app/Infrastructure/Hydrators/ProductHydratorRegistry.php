<?php
declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

class ProductHydratorRegistry
{
    private array $hydrators = [];

    public function register(string $type, ProductHydratorInterface $hydrator): void
    {
        $this->hydrators[$type] = $hydrator;
    }

    public function get(string $type): ProductHydratorInterface
    {
        if (!isset($this->hydrators[$type])) {
            throw new \InvalidArgumentException("No hydrator registered for type: {$type}");
        }
        return $this->hydrators[$type];
    }
}