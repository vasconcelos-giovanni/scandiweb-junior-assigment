<?php

declare(strict_types=1);

use App\Core\Response;
use App\Entities\DvdProduct;
use App\Http\Controllers\ProductController;
use App\Entities\Product;

$router->get('/test', function () {
    $dvd = new DvdProduct();
    $dvd->setId(1)
        ->setSku('DVD-001')
        ->setName('Inception')
        ->setPrice(19.99)
        ->setSize(700);

    dd($dvd);

    return Response::json(['message' => 'Hello, world!']);
});
