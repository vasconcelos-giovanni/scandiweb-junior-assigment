<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\ProductService;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\ValidationException;

/**
 * Controller for Product HTTP endpoints.
 *
 * Handles HTTP requests for product CRUD operations.
 * Uses ProductService for business logic.
 */
class ProductController
{
    /** @var ProductService */
    private ProductService $productService;

    /**
     * Create a new ProductController instance.
     *
     * @param ProductService $productService The product service.
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * GET /products
     *
     * List all products.
     *
     * @return Response JSON array of products.
     */
    public function index(): Response
    {
        $products = $this->productService->getAllProducts();

        return Response::json($products);
    }

    /**
     * GET /products/{id}
     *
     * Get a single product by ID.
     *
     * @param Request $request The HTTP request.
     * @return Response JSON product data or 404 error.
     */
    public function show(Request $request): Response
    {
        // Extract ID from query params or route
        $id = (int)$request->getQuery('id', 0);

        if ($id <= 0) {
            return Response::error('Invalid product ID', null, 400);
        }

        $product = $this->productService->getProductById($id);

        if ($product === null) {
            return Response::notFound('Product not found');
        }

        return Response::json($product);
    }

    /**
     * POST /products
     *
     * Create a new product.
     * Handles all product types (DVD, Book, Furniture) through a single endpoint.
     *
     * @param Request $request The HTTP request with JSON body.
     * @return Response JSON response with created status or validation errors.
     */
    public function store(Request $request): Response
    {
        $data = $request->getJson();

        if ($data === null) {
            return Response::error('Invalid JSON data', null, 400);
        }

        $product = $this->productService->createProduct($data);

        return Response::created(
            $product->toArray(),
            'Product created successfully.'
        );
    }

    /**
     * DELETE /products
     *
     * Mass delete products by IDs.
     * Expects JSON body: { "ids": [1, 2, ...] }
     *
     * @param Request $request The HTTP request with JSON body.
     * @return Response JSON response with deleted count.
     */
    public function destroy(Request $request): Response
    {
        $data = $request->getJson();

        if ($data === null) {
            return Response::error('Invalid JSON data', null, 400);
        }

        if (isset($data['ids']) && is_array($data['ids'])) {
            $ids = array_map('intval', $data['ids']);
            $deletedCount = $this->productService->deleteProductsByIds($ids);
        } else {
            throw new ValidationException([
                'ids' => 'Please provide an array of IDs to delete.'
            ]);
        }

        return Response::json([
            'message' => 'Products deleted successfully.'
        ]);
    }
}
