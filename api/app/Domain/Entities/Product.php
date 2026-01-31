<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Core\Entity;
use App\Domain\Contracts\ProductInterface;

/**
 * Abstract base class for all product types in the domain.
 *
 * Serves as the parent in a Class Table Inheritance (CTI) hierarchy.
 * Each product type (DVD, Book, Furniture) extends this class and
 * stores type-specific attributes in a separate table.
 *
 * @Table(name="products")
 * @DiscriminatorColumn(name="type")
 * @DiscriminatorMap(
 *     dvd="App\\Domain\\Entities\\DvdProduct",
 *     book="App\\Domain\\Entities\\BookProduct",
 *     furniture="App\\Domain\\Entities\\FurnitureProduct"
 * )
 */
abstract class Product extends Entity implements ProductInterface
{
    /**
     * The unique identifier.
     *
     * @Column(type="INT", options="PRIMARY KEY AUTO_INCREMENT")
     * @var int
     */
    protected int $id = 0;

    /**
     * The Stock Keeping Unit - must be unique.
     *
     * @Column(type="VARCHAR(255)", options="NOT NULL UNIQUE")
     * @var string
     */
    protected string $sku = '';

    /**
     * The product name.
     *
     * @Column(type="VARCHAR(255)", options="NOT NULL")
     * @var string
     */
    protected string $name = '';

    /**
     * The product price.
     *
     * @Column(type="DECIMAL(10, 2)", options="NOT NULL")
     * @var float
     */
    protected float $price = 0.0;

    // =========================================================================
    // GETTERS
    // =========================================================================

    /**
     * {@inheritDoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function getType(): string;

    // =========================================================================
    // SETTERS
    // =========================================================================

    /**
     * Set the product ID.
     *
     * @param int $id The product ID.
     * @return static
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the product SKU.
     *
     * @param string $sku The Stock Keeping Unit.
     * @return static
     */
    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * Set the product name.
     *
     * @param string $name The product name.
     * @return static
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the product price.
     *
     * @param float $price The product price.
     * @return static
     * @throws \InvalidArgumentException If price is negative.
     */
    public function setPrice(float $price): self
    {
        if ($price < 0) {
            throw new \InvalidArgumentException("Price cannot be negative.");
        }
        $this->price = $price;
        return $this;
    }

    // =========================================================================
    // ABSTRACT METHODS
    // =========================================================================

    /**
     * {@inheritDoc}
     */
    abstract public function getSpecificAttribute(): string;

    /**
     * {@inheritDoc}
     */
    abstract public function getSpecificAttributesArray(): array;

    // =========================================================================
    // ARRAY CONVERSION
    // =========================================================================

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'sku' => $this->getSku(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'type' => $this->getType(),
            'specific_attribute' => $this->getSpecificAttribute(),
        ];
    }
}
