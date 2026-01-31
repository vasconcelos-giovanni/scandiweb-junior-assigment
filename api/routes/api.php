<?php

declare(strict_types=1);

use App\Core\Response;
use App\Http\Controllers\ProductController;

/**
 * API Routes
 *
 * All routes are prefixed with /api in production.
 * The $router variable is injected by the RouteServiceProvider.
 */

// ============================================================================
// Health Check / Test Routes
// ============================================================================

$router->get('/test', function () {
    return Response::json([
        'message' => 'API is working!',
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
});

// ============================================================================
// Product Routes
// ============================================================================

/**
 * GET /products
 *
 * List all products with their type-specific attributes.
 * Products are sorted by ID (primary key) as required by Scandiweb.
 */
$router->get('/products', [ProductController::class, 'index']);

/**
 * POST /products
 *
 * Create a new product.
 * Handles all product types (DVD, Book, Furniture) through a single endpoint.
 *
 * Request body (JSON):
 * - sku: string (required, unique)
 * - name: string (required)
 * - price: number (required)
 * - type: string (required: 'dvd' | 'book' | 'furniture')
 * - size: number (required for DVD, in MB)
 * - weight: number (required for Book, in Kg)
 * - height: number (required for Furniture)
 * - width: number (required for Furniture)
 * - length: number (required for Furniture)
 */
$router->post('/products', [ProductController::class, 'store']);

/**
 * DELETE /products
 *
 * Mass delete products.
 *
 * Request body (JSON):
 * - skus: string[] (array of SKUs to delete)
 *   OR
 * - ids: number[] (array of IDs to delete)
 */
$router->delete('/products', [ProductController::class, 'destroy']);
