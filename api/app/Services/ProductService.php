<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DuplicateSkuException;
use App\Infrastructure\Factories\ProductFactoryResolver;
use App\Repositories\ProductRepository;

class ProductService
{
    private ProductRepository $productRepository;
    private ProductFactoryResolver $factoryResolver;

    public function __construct(ProductRepository $productRepository, ProductFactoryResolver $factoryResolver)
    {
        $this->productRepository = $productRepository;
        $this->factoryResolver = $factoryResolver;
    }

    public function getAllProducts(): array
    {
        $products = $this->productRepository->findAll();
        
        // Convert product objects to simple arrays for the JSON response
        return array_map(fn($product) => $product->toArray(), $products);
    }

    public function createProduct(array $data): void
    {
        // 1. Business Rule: Check for duplicate SKU
        // if ($this->productRepository->findBySku($data['sku'])) {
        //     throw new DuplicateSkuException("SKU '{$data['sku']}' already exists.");
        // }
        
        // 2. Use the factory to create a domain object from request data
        $factory = $this->factoryResolver->resolve($data['type']);
        $product = $factory->createProduct($data);
        
        // 3. Use the repository to persist the domain object
        $this->productRepository->save($product);
    }

    public function deleteProducts(array $skus): int
    {
        return $this->productRepository->deleteBySkus($skus);
    }
}