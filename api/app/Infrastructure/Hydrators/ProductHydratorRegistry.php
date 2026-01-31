<?php

declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

/**
 * Registry for product hydrators.
 *
 * Provides a central registry for all product type hydrators,
 * allowing lookup by product type without conditional statements.
 */
class ProductHydratorRegistry
{
    /**
     * @var array<string, ProductHydratorInterface>
     */
    private array $hydrators = [];

    /**
     * Register a hydrator for a product type.
     *
     * @param string $type The product type (e.g., 'dvd', 'book', 'furniture').
     * @param ProductHydratorInterface $hydrator The hydrator instance.
     * @return void
     */
    public function register(string $type, ProductHydratorInterface $hydrator): void
    {
        $this->hydrators[$type] = $hydrator;
    }

    /**
     * Get the hydrator for a product type.
     *
     * @param string $type The product type.
     * @return ProductHydratorInterface The hydrator instance.
     * @throws \InvalidArgumentException If no hydrator is registered for the type.
     */
    public function get(string $type): ProductHydratorInterface
    {
        if (!isset($this->hydrators[$type])) {
            throw new \InvalidArgumentException(
                "No hydrator registered for product type: {$type}"
            );
        }

        return $this->hydrators[$type];
    }

    /**
     * Check if a hydrator exists for the given product type.
     *
     * @param string $type The product type.
     * @return bool True if a hydrator is registered.
     */
    public function has(string $type): bool
    {
        return isset($this->hydrators[$type]);
    }

    /**
     * Get all registered product types.
     *
     * @return array<int, string> The registered types.
     */
    public function getRegisteredTypes(): array
    {
        return array_keys($this->hydrators);
    }
}
