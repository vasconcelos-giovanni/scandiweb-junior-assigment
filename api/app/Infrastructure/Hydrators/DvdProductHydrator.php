<?php

declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Domain\Contracts\ProductInterface;
use App\Domain\Entities\DvdProduct;

/**
 * Hydrator for DVD products.
 *
 * Transforms database rows into DvdProduct entities.
 */
class DvdProductHydrator implements ProductHydratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data): ProductInterface
    {
        $product = new DvdProduct();
        $product->setId((int)$data['id']);
        $product->setSku((string)$data['sku']);
        $product->setName((string)$data['name']);
        $product->setPrice((float)$data['price']);
        $product->setSize((int)$data['size']);

        return $product;
    }
}
