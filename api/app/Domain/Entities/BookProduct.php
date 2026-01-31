<?php

declare(strict_types=1);

namespace App\Domain\Entities;

/**
 * Represents a Book product.
 *
 * A Book has a weight attribute measured in kilograms (Kg).
 * Stores its unique attribute in a separate table (book_products).
 *
 * @Table(name="book_products")
 */
class BookProduct extends Product
{
    /**
     * The weight of the book in kilograms (Kg).
     *
     * @Column(type="DECIMAL(10, 2)", options="NOT NULL")
     * @var float
     */
    protected float $weight = 0.0;

    // =========================================================================
    // GETTERS
    // =========================================================================

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return 'book';
    }

    /**
     * Get the book weight in Kg.
     *
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecificAttribute(): string
    {
        return "Weight: {$this->weight} KG";
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecificAttributesArray(): array
    {
        return ['weight' => $this->weight];
    }

    // =========================================================================
    // SETTERS
    // =========================================================================

    /**
     * Set the book weight in Kg.
     *
     * @param float $weight The weight in kilograms.
     * @return self
     */
    public function setWeight(float $weight): self
    {
        if ($weight < 0) {
            throw new \InvalidArgumentException("Weight cannot be negative.");
        }
        $this->weight = $weight;
        return $this;
    }
}
