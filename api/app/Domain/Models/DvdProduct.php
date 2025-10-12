<?php
declare(strict_types=1);

namespace App\Domain\Models;

class DvdProduct extends Product
{
    private int $size;

    public function __construct(string $sku, string $name, float $price, int $size)
    {
        parent::__construct($sku, $name, $price);
        $this->size = $size;
    }

    public function getType(): string
    {
        return 'dvd';
    }

    public function getSpecificAttribute(): string
    {
        return "Size: {$this->size} MB";
    }

    public function getSpecificAttributesArray(): array
    {
        return ['size' => $this->size];
    }
}