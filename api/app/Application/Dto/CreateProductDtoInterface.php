<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Interface for all product creation DTOs.
 *
 * Each product type implements this interface to provide
 * type-specific validation and data transfer.
 */
interface CreateProductDtoInterface
{
    /**
     * Get the product type identifier.
     *
     * @return string The type (e.g., 'dvd', 'book', 'furniture').
     */
    public static function getType(): string;

    /**
     * Create a DTO instance from an array of data.
     *
     * @param array<string, mixed> $data The input data.
     * @return static The validated DTO instance.
     * @throws \App\Exceptions\ValidationException If validation fails.
     */
    public static function fromArray(array $data);

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

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
