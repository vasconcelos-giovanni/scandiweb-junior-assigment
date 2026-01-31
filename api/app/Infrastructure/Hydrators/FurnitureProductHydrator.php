<?php

declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Core\Entity;
use App\Domain\Entities\FurnitureProduct;

/**
 * Hydrator for Furniture products.
 *
 * Transforms database rows into FurnitureProduct entities.
 */
class FurnitureProductHydrator implements ProductHydratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data): Entity
    {
        $product = new FurnitureProduct();
        $product->setId((int)$data['id']);
        $product->setSku((string)$data['sku']);
        $product->setName((string)$data['name']);
        $product->setPrice((float)$data['price']);
        $product->setHeight((int)$data['height']);
        $product->setWidth((int)$data['width']);
        $product->setLength((int)$data['length']);

        return $product;
    }
}
