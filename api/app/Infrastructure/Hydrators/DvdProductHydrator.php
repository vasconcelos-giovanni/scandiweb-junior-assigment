<?php
declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Domain\Models\DvdProduct;
use App\Domain\Models\ProductInterface;

class DvdProductHydrator implements ProductHydratorInterface
{
    public function hydrate(array $data): ProductInterface
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