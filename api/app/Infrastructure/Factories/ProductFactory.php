<?php

declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Application\Dto\CreateProductDtoInterface;
use App\Domain\Contracts\ProductInterface;

/**
 * Abstract base factory for creating Product entities.
 *
 * Each product type has its own factory implementation,
 * avoiding conditional statements for type handling.
 * This follows the Factory Method pattern as required
 * by Scandiweb guidelines.
 */
abstract class ProductFactory
{
    /**
     * Create a new Product entity from a DTO.
     *
     * @param CreateProductDtoInterface $dto The validated DTO.
     * @return ProductInterface The created product entity.
     */
    abstract public function createFromDto(CreateProductDtoInterface $dto): ProductInterface;

    /**
     * Create a new Product entity from an array of data.
     *
     * @param array<string, mixed> $data The product data.
     * @return ProductInterface The created product entity.
     */
    abstract public function createFromArray(array $data): ProductInterface;
}
