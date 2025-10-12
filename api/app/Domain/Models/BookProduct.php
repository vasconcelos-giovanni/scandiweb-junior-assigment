<?php
declare(strict_types=1);

namespace App\Domain\Models;

class BookProduct extends Product
{
    private float $weight;

    public function __construct(string $sku, string $name, float $price, float $weight)
    {
        parent::__construct($sku, $name, $price);
        $this->weight = $weight;
    }

    public function getType(): string
    {
        return 'book';
    }

    public function getSpecificAttribute(): string
    {
        return "Weight: {$this->weight} KG";
    }

    public function getSpecificAttributesArray(): array
    {
        return ['weight' => $this->weight];
    }
}