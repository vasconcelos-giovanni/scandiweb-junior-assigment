<?php

declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Application\Dto\CreateDvdDto;
use App\Application\Dto\CreateProductDtoInterface;
use App\Domain\Contracts\ProductInterface;
use App\Domain\Entities\DvdProduct;

/**
 * Factory for creating DVD product entities.
 */
class DvdProductFactory extends ProductFactory
{
    /**
     * {@inheritDoc}
     *
     * @param CreateDvdDto $dto
     */
    public function createFromDto(CreateProductDtoInterface $dto): ProductInterface
    {
        /** @var CreateDvdDto $dto */
        $product = new DvdProduct();
        $product->setSku($dto->getSku());
        $product->setName($dto->getName());
        $product->setPrice($dto->getPrice());
        $product->setSize($dto->getSize());

        return $product;
    }

    /**
     * {@inheritDoc}
     */
    public function createFromArray(array $data): ProductInterface
    {
        $product = new DvdProduct();

        if (isset($data['id'])) {
            $product->setId((int)$data['id']);
        }

        $product->setSku((string)$data['sku']);
        $product->setName((string)$data['name']);
        $product->setPrice((float)$data['price']);
        $product->setSize((int)$data['size']);

        return $product;
    }
}
