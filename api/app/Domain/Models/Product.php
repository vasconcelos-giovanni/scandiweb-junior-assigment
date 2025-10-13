<?php
declare(strict_types=1);

namespace App\Domain\Models;

abstract class Product implements ProductInterface
{
    protected int $id;
    protected string $sku;
    protected string $name;
    protected float $price;

    public function __construct(int $id, string $sku, string $name, float $price)
    {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

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

    abstract public function getType(): string;
    abstract public function getSpecificAttribute(): string;
    abstract public function getSpecificAttributesArray(): array;
}