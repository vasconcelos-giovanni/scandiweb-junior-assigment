<?php

declare(strict_types=1);

// ============================================================================
// CORS Headers - Allow requests from frontend
// ============================================================================
$allowedOrigins = [
    'http://localhost:3000',
    'http://127.0.0.1:3000',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
} elseif (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/', $origin)) {
    // For development, allow any localhost origin
    header("Access-Control-Allow-Origin: $origin");
}

header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

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
