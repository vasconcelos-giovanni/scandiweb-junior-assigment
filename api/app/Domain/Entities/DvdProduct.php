<?php

declare(strict_types=1);

namespace App\Domain\Entities;

/**
 * Represents a DVD product.
 *
 * A DVD has a size attribute measured in megabytes (MB).
 * Stores its unique attribute in a separate table (dvd_products).
 *
 * @Table(name="dvd_products")
 */
class DvdProduct extends Product
{
    /**
     * The size of the DVD in megabytes (MB).
     *
     * @Column(type="INT", options="NOT NULL")
     * @var int
     */
    protected int $size = 0;

    // =========================================================================
    // GETTERS
    // =========================================================================

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return 'dvd';
    }

    /**
     * Get the DVD size in MB.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecificAttribute(): string
    {
        return "Size: {$this->size} MB";
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecificAttributesArray(): array
    {
        return ['size' => $this->size];
    }

    // =========================================================================
    // SETTERS
    // =========================================================================

    /**
     * Set the DVD size in MB.
     *
     * @param int $size The size in megabytes.
     * @return self
     */
    public function setSize(int $size): self
    {
        if ($size < 0) {
            throw new \InvalidArgumentException("Size cannot be negative.");
        }
        $this->size = $size;
        return $this;
    }
}
