<?php
declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Domain\Models\DvdProduct;
use App\Domain\Models\ProductInterface;

class DvdProductFactory extends ProductFactory
{
    public function createProduct(array $data): ProductInterface
    {
        return new DvdProduct(
            (int)$data['id'],
            $data['sku'],
            $data['name'],
            (float)$data['price'],
            (int)$data['size']
        );
    }
}