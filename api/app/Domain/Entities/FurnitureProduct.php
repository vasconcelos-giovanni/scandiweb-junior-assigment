<?php

declare(strict_types=1);

namespace App\Domain\Entities;

/**
 * Represents a Furniture product.
 *
 * Furniture has dimension attributes: height, width, and length.
 * Stores its unique attributes in a separate table (furniture_products).
 *
 * @Table(name="furniture_products")
 */
class FurnitureProduct extends Product
{
    /**
     * The height dimension in centimeters.
     *
     * @Column(type="INT", options="NOT NULL")
     * @var int
     */
    protected int $height = 0;

    /**
     * The width dimension in centimeters.
     *
     * @Column(type="INT", options="NOT NULL")
     * @var int
     */
    protected int $width = 0;

    /**
     * The length dimension in centimeters.
     *
     * @Column(type="INT", options="NOT NULL")
     * @var int
     */
    protected int $length = 0;

    // =========================================================================
    // GETTERS
    // =========================================================================

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return 'furniture';
    }

    /**
     * Get the height dimension.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the width dimension.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get the length dimension.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecificAttribute(): string
    {
        return "Dimension: {$this->height}x{$this->width}x{$this->length}";
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecificAttributesArray(): array
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
        ];
    }

    // =========================================================================
    // SETTERS
    // =========================================================================

    /**
     * Set the height dimension.
     *
     * @param int $height The height in centimeters.
     * @return self
     */
    public function setHeight(int $height): self
    {
        if ($height < 0) {
            throw new \InvalidArgumentException("Height cannot be negative.");
        }
        $this->height = $height;
        return $this;
    }

    /**
     * Set the width dimension.
     *
     * @param int $width The width in centimeters.
     * @return self
     */
    public function setWidth(int $width): self
    {
        if ($width < 0) {
            throw new \InvalidArgumentException("Width cannot be negative.");
        }
        $this->width = $width;
        return $this;
    }

    /**
     * Set the length dimension.
     *
     * @param int $length The length in centimeters.
     * @return self
     */
    public function setLength(int $length): self
    {
        if ($length < 0) {
            throw new \InvalidArgumentException("Length cannot be negative.");
        }
        $this->length = $length;
        return $this;
    }
}
