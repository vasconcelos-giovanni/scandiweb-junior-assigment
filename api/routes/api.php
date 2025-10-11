<?php
declare(strict_types= 1);

use App\Core\Response;

$router->get('/test', function () {
    return Response::json(['message' => 'Hello, World!']);
});