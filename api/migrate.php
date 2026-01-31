<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\App;
use App\Core\Database;
use App\Core\Migration;
use App\Entities\Product;

/**
 * Define the "root" entities of your application.
 * The generator will automatically find their children via the DiscriminatorMap.
 */
$rootEntities = [
    // Only root entities should be listed here. Children are discovered via the discriminator map.
    Product::class,
];

echo "==============================\n";
echo "   Database Migration Tool    \n";
echo "==============================\n\n";

try {
    // Bootstrap the application to get access to the container and services.
    $app = new App();
    $database = $app->getContainer()->make(Database::class);

    // Instantiate the migration runner with the database connection.
    $migration = new Migration($database);

    // Run the migrations for our defined root entities.
    $migration->run($rootEntities);

} catch (\Exception $e) {
    echo "An unexpected error occurred: " . $e->getMessage() . "\n";
    exit(1); // Exit with an error code
}