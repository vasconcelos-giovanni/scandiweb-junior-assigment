<?php
declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Domain\Models\FurnitureProduct;
use App\Domain\Models\ProductInterface;

class FurnitureProductHydrator implements ProductHydratorInterface
{
    public function hydrate(array $data): ProductInterface
    {
        return new FurnitureProduct(
            $data['sku'],
            $data['name'],
            (float)$data['price'],
            (int)$data['height'],
            (int)$data['width'],
            (int)$data['length']
        );
    }
}