<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Providers\ProductServiceProvider;
use App\Providers\RouteServiceProvider;

// Create the application instance
$app = new App();

// Register service providers
$app->register(new RouteServiceProvider($app->getContainer()));
$app->register(new ProductServiceProvider($app->getContainer()));

$app->run();