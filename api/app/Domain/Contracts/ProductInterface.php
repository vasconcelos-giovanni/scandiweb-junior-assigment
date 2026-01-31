<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

/**
 * Contract for all Product entities.
 *
 * Defines the interface that all product types must implement,
 * ensuring consistent behavior across DVD, Book, and Furniture products.
 * This enables polymorphism without conditional statements.
 */
interface ProductInterface
{
    /**
     * Get the product ID.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get the product SKU (Stock Keeping Unit).
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Get the product name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the product price.
     *
     * @return float
     */
    public function getPrice(): float;

    /**
     * Get the product type identifier.
     *
     * @return string The type (e.g., 'dvd', 'book', 'furniture').
     */
    public function getType(): string;

    /**
     * Set the product ID.
     *
     * @param int $id
     * @return static
     */
    public function setId(int $id);

    /**
     * Set the product SKU.
     *
     * @param string $sku
     * @return static
     */
    public function setSku(string $sku);

    /**
     * Set the product name.
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name);

    /**
     * Set the product price.
     *
     * @param float $price
     * @return static
     */
    public function setPrice(float $price);

    /**
     * Get the type-specific attribute formatted for display.
     *
     * @return string Formatted string (e.g., "Size: 700 MB", "Weight: 2 KG").
     */
    public function getSpecificAttribute(): string;

    /**
     * Get type-specific attributes as an associative array.
     *
     * @return array<string, mixed> The specific attributes.
     */
    public function getSpecificAttributesArray(): array;

    /**
     * Convert the product to an array representation.
     *
     * @return array<string, mixed> The product data.
     */
    public function toArray(): array;
}
