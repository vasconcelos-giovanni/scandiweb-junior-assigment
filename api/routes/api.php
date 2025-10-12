<?php
declare(strict_types= 1);

use App\Core\Response;
use App\Services\ProductService;

$router->get('/test', function () {
    return Response::json(['message' => 'Hello, World!']);
});

$router->get('/products', function (ProductService $productService) {
    $products = $productService->getAllProducts();
    return Response::json($products);
});