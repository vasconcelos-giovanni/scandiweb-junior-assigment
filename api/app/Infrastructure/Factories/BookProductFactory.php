<?php
declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Domain\Models\BookProduct;
use App\Domain\Models\ProductInterface;

class BookProductFactory extends ProductFactory
{
    public function createProduct(array $data): ProductInterface
    {
        return new BookProduct(
            $data['sku'],
            $data['name'],
            (float)$data['price'],
            (float)$data['weight']
        );
    }
}