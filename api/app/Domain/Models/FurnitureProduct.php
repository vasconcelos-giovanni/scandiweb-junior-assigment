<?php
declare(strict_types=1);

namespace App\Domain\Models;

class FurnitureProduct extends Product
{
    private int $height;
    private int $width;
    private int $length;

    public function __construct(int $id, string $sku, string $name, float $price, int $height, int $width, int $length)
    {
        parent::__construct($id, $sku, $name, $price);
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public function getType(): string
    {
        return 'furniture';
    }

    public function getSpecificAttribute(): string
    {
        return "Dimension: {$this->height}x{$this->width}x{$this->length}";
    }

    public function getSpecificAttributesArray(): array
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
        ];
    }
}