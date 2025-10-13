<?php
declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Domain\Models\BookProduct;
use App\Domain\Models\ProductInterface;

class BookProductHydrator implements ProductHydratorInterface
{
    public function hydrate(array $data): ProductInterface
    {
        return new BookProduct(
            (int)$data['id'],
            $data['sku'],
            $data['name'],
            (float)$data['price'],
            (float)$data['weight']
        );
    }
}