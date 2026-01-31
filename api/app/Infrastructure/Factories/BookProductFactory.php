<?php

declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Application\Dto\CreateBookDto;
use App\Application\Dto\CreateProductDtoInterface;
use App\Domain\Contracts\ProductInterface;
use App\Domain\Entities\BookProduct;

/**
 * Factory for creating Book product entities.
 */
class BookProductFactory extends ProductFactory
{
    /**
     * {@inheritDoc}
     *
     * @param CreateBookDto $dto
     */
    public function createFromDto(CreateProductDtoInterface $dto): ProductInterface
    {
        /** @var CreateBookDto $dto */
        $product = new BookProduct();
        $product->setSku($dto->getSku());
        $product->setName($dto->getName());
        $product->setPrice($dto->getPrice());
        $product->setWeight($dto->getWeight());

        return $product;
    }

    /**
     * {@inheritDoc}
     */
    public function createFromArray(array $data): ProductInterface
    {
        $product = new BookProduct();

        if (isset($data['id'])) {
            $product->setId((int)$data['id']);
        }

        $product->setSku((string)$data['sku']);
        $product->setName((string)$data['name']);
        $product->setPrice((float)$data['price']);
        $product->setWeight((float)$data['weight']);

        return $product;
    }
}
