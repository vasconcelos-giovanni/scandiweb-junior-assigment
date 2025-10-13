<?php
declare(strict_types= 1);

use App\Core\Response;
use App\Http\Controllers\ProductController;


$router->get('/test', function () {
    return Response::json(['message' => 'Hello, World!']);
});

$router->get('/products', [ProductController::class, 'index']);

$router->post('/products', [ProductController::class, 'store']);