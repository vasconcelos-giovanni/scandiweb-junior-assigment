<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Dto\CreateProductDtoInterface;
use App\Application\Dto\DtoResolver;
use App\Domain\Contracts\ProductInterface;
use App\Exceptions\DuplicateSkuException;
use App\Infrastructure\Factories\ProductFactoryResolver;
use App\Infrastructure\Repositories\ProductRepository;

/**
 * Service layer for Product business logic.
 *
 * Orchestrates DTOs, Factories, and Repository to handle
 * product-related operations. Acts as the main entry point
 * for controllers.
 */
class ProductService
{
    /** @var ProductRepository */
    private ProductRepository $productRepository;

    /** @var ProductFactoryResolver */
    private ProductFactoryResolver $factoryResolver;

    /** @var DtoResolver */
    private DtoResolver $dtoResolver;

    /**
     * Create a new ProductService instance.
     *
     * @param ProductRepository $productRepository The product repository.
     * @param ProductFactoryResolver $factoryResolver The factory resolver.
     * @param DtoResolver $dtoResolver The DTO resolver.
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductFactoryResolver $factoryResolver,
        DtoResolver $dtoResolver
    ) {
        $this->productRepository = $productRepository;
        $this->factoryResolver = $factoryResolver;
        $this->dtoResolver = $dtoResolver;
    }

    /**
     * Get all products.
     *
     * @return array<int, array<string, mixed>> Array of product data.
     */
    public function getAllProducts(): array
    {
        $products = $this->productRepository->findAll();

        // Convert entities to arrays for JSON response
        return array_map(function (ProductInterface $product): array {
            return $product->toArray();
        }, $products);
    }

    /**
     * Get a single product by ID.
     *
     * @param int $id The product ID.
     * @return array<string, mixed>|null The product data or null if not found.
     */
    public function getProductById(int $id): ?array
    {
        $product = $this->productRepository->findById($id);

        return $product ? $product->toArray() : null;
    }

    /**
     * Get a single product by SKU.
     *
     * @param string $sku The product SKU.
     * @return array<string, mixed>|null The product data or null if not found.
     */
    public function getProductBySku(string $sku): ?array
    {
        $product = $this->productRepository->findBySku($sku);

        return $product ? $product->toArray() : null;
    }

    /**
     * Create a new product from request data.
     *
     * This method:
     * 1. Validates input data using the appropriate DTO
     * 2. Creates the product entity using the appropriate Factory
     * 3. Persists the product through the Repository
     *
     * @param array<string, mixed> $data The request data.
     * @return ProductInterface The created product entity.
     * @throws \App\Exceptions\ValidationException If validation fails.
     * @throws DuplicateSkuException If SKU already exists.
     */
    public function createProduct(array $data): ProductInterface
    {
        // 1. Validate and create DTO (polymorphic validation)
        $dto = $this->dtoResolver->resolve($data);

        // 2. Get the appropriate factory and create entity
        $factory = $this->factoryResolver->resolve($dto::getType());
        $product = $factory->createFromDto($dto);

        // 3. Persist the product
        return $this->productRepository->save($product);
    }

    /**
     * Create a product directly from a DTO.
     *
     * @param CreateProductDtoInterface $dto The validated DTO.
     * @return ProductInterface The created product entity.
     * @throws DuplicateSkuException If SKU already exists.
     */
    public function createProductFromDto(CreateProductDtoInterface $dto): ProductInterface
    {
        $factory = $this->factoryResolver->resolve($dto::getType());
        $product = $factory->createFromDto($dto);

        return $this->productRepository->save($product);
    }

    /**
     * Delete products by their SKUs.
     *
     * @param array<int, string> $skus The SKUs to delete.
     * @return int The number of deleted products.
     */
    public function deleteProductsBySkus(array $skus): int
    {
        return $this->productRepository->deleteBySkus($skus);
    }

    /**
     * Delete products by their IDs.
     *
     * @param array<int, int> $ids The IDs to delete.
     * @return int The number of deleted products.
     */
    public function deleteProductsByIds(array $ids): int
    {
        return $this->productRepository->deleteByIds($ids);
    }

    /**
     * Check if a SKU already exists.
     *
     * @param string $sku The SKU to check.
     * @return bool True if the SKU exists.
     */
    public function skuExists(string $sku): bool
    {
        return $this->productRepository->skuExists($sku);
    }
}
