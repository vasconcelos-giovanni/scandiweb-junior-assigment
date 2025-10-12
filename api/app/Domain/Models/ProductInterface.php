<?php
declare(strict_types=1);

namespace App\Domain\Models;

interface ProductInterface
{
    public function getSku(): string;
    public function getName(): string;
    public function getPrice(): float;
    public function getType(): string;
    public function getSpecificAttribute(): string;
    public function toArray(): array;
}