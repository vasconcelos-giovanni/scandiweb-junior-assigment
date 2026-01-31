<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Core\DtoInterface;

/**
 * Interface for all product creation DTOs.
 *
 * Each product type implements this interface to provide
 * type-specific validation and data transfer.
 */
interface CreateProductDtoInterface extends DtoInterface
{
    /**
     * Get the product type identifier.
     *
     * @return string The type (e.g., 'dvd', 'book', 'furniture').
     */
    public static function getType(): string;

    /**
     * Get the SKU.
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the price.
     *
     * @return float
     */
    public function getPrice(): float;
}
