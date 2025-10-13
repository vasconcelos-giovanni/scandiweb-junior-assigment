<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\Validation\ProductValidationStrategyResolver;
use App\Services\ProductService;

class ProductController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    
    public function index(): Response
    {
        $products = $this->productService->getAllProducts();
        return Response::json($products);
    }

    public function store(Request $request, ProductValidationStrategyResolver $resolver): Response
    {
        $validator = new StoreProductRequest($request, $resolver);

        $validatedData = $validator->validated();

        $this->productService->createProduct($validatedData);

        return Response::created(null, 'Product created successfully.');
    }
}