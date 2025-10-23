<?php
declare(strict_types=1);

namespace App\Entities;

/**
 * Represents a DVD, which is a specific type of Product.
 * It stores its unique attribute, size, in a separate table.
 */
class DvdProduct extends Product
{
    /**
     * The size of the DVD in megabytes (MB).
     * @Column(type="INT", options="NOT NULL")
     */
    protected int $size;

    // --- Implementation of the Entity Contract ---

    public static function getTableName(): string
    {
        return 'dvd_products';
    }

    // --- Getters ---

    public function getSize(): int
    {
        return $this->size;
    }

    // --- Setters ---

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }
}