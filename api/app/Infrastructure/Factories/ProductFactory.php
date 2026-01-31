<?php

declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Application\Dto\CreateProductDtoInterface;
use App\Core\DtoInterface;
use App\Core\Entity;
use App\Core\FactoryInterface;
use App\Domain\Contracts\ProductInterface;

/**
 * Abstract base factory for creating Product entities.
 *
 * Each product type has its own factory implementation,
 * avoiding conditional statements for type handling.
 * This follows the Factory Method pattern as required
 * by Scandiweb guidelines.
 */
abstract class ProductFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @param DtoInterface $dto The validated DTO.
     * @return Entity The created product entity.
     */
    public function createFromDto(DtoInterface $dto): Entity
    {
        /** @var CreateProductDtoInterface $dto */
        return $this->createFromProductDto($dto);
    }

    /**
     * {@inheritDoc}
     */
    abstract public function createFromArray(array $data): Entity;

    /**
     * Create a new Product entity from a Product DTO.
     *
     * @param CreateProductDtoInterface $dto The validated DTO.
     * @return ProductInterface The created product entity.
     */
    abstract public function createFromProductDto(CreateProductDtoInterface $dto): ProductInterface;
}
