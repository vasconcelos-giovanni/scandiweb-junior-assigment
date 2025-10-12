<?php
declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Domain\Models\ProductInterface;

abstract class ProductFactory
{
    abstract public function createProduct(array $data): ProductInterface;
}