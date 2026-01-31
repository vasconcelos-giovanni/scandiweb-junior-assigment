<?php

declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Core\Entity;
use App\Domain\Entities\BookProduct;

/**
 * Hydrator for Book products.
 *
 * Transforms database rows into BookProduct entities.
 */
class BookProductHydrator implements ProductHydratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data): Entity
    {
        $product = new BookProduct();
        $product->setId((int)$data['id']);
        $product->setSku((string)$data['sku']);
        $product->setName((string)$data['name']);
        $product->setPrice((float)$data['price']);
        $product->setWeight((float)$data['weight']);

        return $product;
    }
}
