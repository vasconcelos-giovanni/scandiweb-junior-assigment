<?php
declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Domain\Models\FurnitureProduct;
use App\Domain\Models\ProductInterface;

class FurnitureProductFactory extends ProductFactory
{
    public function createProduct(array $data): ProductInterface
    {
        return new FurnitureProduct(
            (int)$data['id'],
            $data['sku'],
            $data['name'],
            (float)$data['price'],
            (int)$data['height'],
            (int)$data['width'],
            (int)$data['length']
        );
    }
}