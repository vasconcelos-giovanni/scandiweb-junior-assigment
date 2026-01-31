<?php

declare(strict_types=1);

namespace App\Infrastructure\Factories;

use App\Application\Dto\CreateFurnitureDto;
use App\Application\Dto\CreateProductDtoInterface;
use App\Domain\Contracts\ProductInterface;
use App\Domain\Entities\FurnitureProduct;

/**
 * Factory for creating Furniture product entities.
 */
class FurnitureProductFactory extends ProductFactory
{
    /**
     * {@inheritDoc}
     *
     * @param CreateFurnitureDto $dto
     */
    public function createFromDto(CreateProductDtoInterface $dto): ProductInterface
    {
        /** @var CreateFurnitureDto $dto */
        $product = new FurnitureProduct();
        $product->setSku($dto->getSku());
        $product->setName($dto->getName());
        $product->setPrice($dto->getPrice());
        $product->setHeight($dto->getHeight());
        $product->setWidth($dto->getWidth());
        $product->setLength($dto->getLength());

        return $product;
    }

    /**
     * {@inheritDoc}
     */
    public function createFromArray(array $data): ProductInterface
    {
        $product = new FurnitureProduct();

        if (isset($data['id'])) {
            $product->setId((int)$data['id']);
        }

        $product->setSku((string)$data['sku']);
        $product->setName((string)$data['name']);
        $product->setPrice((float)$data['price']);
        $product->setHeight((int)$data['height']);
        $product->setWidth((int)$data['width']);
        $product->setLength((int)$data['length']);

        return $product;
    }
}
