<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Core\Database;
use App\Core\DB;
use App\Core\Entity;
use App\Core\Repository;
use App\Domain\Contracts\ProductInterface;
use App\Domain\Entities\Product;
use App\Exceptions\DuplicateSkuException;
use App\Infrastructure\Hydrators\ProductHydratorRegistry;

/**
 * Repository for Product entities.
 *
 * Handles data access for products using Class Table Inheritance (CTI).
 * The parent products table stores common attributes, while child tables
 * (dvd_products, book_products, furniture_products) store type-specific attributes.
 */
class ProductRepository extends Repository
{
    /** @var ProductHydratorRegistry */
    private ProductHydratorRegistry $hydratorRegistry;

    /**
     * Create a new ProductRepository instance.
     *
     * @param Database $db The database connection.
     * @param ProductHydratorRegistry $hydratorRegistry The hydrator registry.
     */
    public function __construct(Database $db, ProductHydratorRegistry $hydratorRegistry)
    {
        parent::__construct($db);
        $this->hydratorRegistry = $hydratorRegistry;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEntityClass(): string
    {
        return Product::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function hydrate(array $data): Entity
    {
        $type = (string)$data['type'];
        $hydrator = $this->hydratorRegistry->get($type);

        /** @var Entity $entity */
        $entity = $hydrator->hydrate($data);

        return $entity;
    }

    /**
     * Find all products with their type-specific attributes.
     *
     * Uses LEFT JOINs to fetch all product types in a single query.
     *
     * @return array<int, ProductInterface> Array of product entities.
     */
    public function findAll(): array
    {
        $rows = DB::table('products')
            ->select([
                'products.id',
                'products.sku',
                'products.name',
                'products.price',
                'products.type',
                'dvd_products.size',
                'book_products.weight',
                'furniture_products.height',
                'furniture_products.width',
                'furniture_products.length'
            ])
            ->leftJoin('dvd_products', 'products.id', '=', 'dvd_products.id')
            ->leftJoin('book_products', 'products.id', '=', 'book_products.id')
            ->leftJoin('furniture_products', 'products.id', '=', 'furniture_products.id')
            ->orderBy('products.id', 'ASC')
            ->get();

        $products = [];
        foreach ($rows as $data) {
            $products[] = $this->hydrate($data);
        }

        return $products;
    }

    /**
     * Find a product by ID with type-specific attributes.
     *
     * @param int $id The product ID.
     * @return ProductInterface|null The product or null if not found.
     */
    public function findById(int $id): ?ProductInterface
    {
        $data = DB::table('products')
            ->select([
                'products.id',
                'products.sku',
                'products.name',
                'products.price',
                'products.type',
                'dvd_products.size',
                'book_products.weight',
                'furniture_products.height',
                'furniture_products.width',
                'furniture_products.length'
            ])
            ->leftJoin('dvd_products', 'products.id', '=', 'dvd_products.id')
            ->leftJoin('book_products', 'products.id', '=', 'book_products.id')
            ->leftJoin('furniture_products', 'products.id', '=', 'furniture_products.id')
            ->where('products.id', '=', $id)
            ->first();

        if (!$data) {
            return null;
        }

        /** @var ProductInterface $product */
        $product = $this->hydrate($data);

        return $product;
    }

    /**
     * Find a product by SKU.
     *
     * @param string $sku The SKU to search for.
     * @return ProductInterface|null The product or null if not found.
     */
    public function findBySku(string $sku): ?ProductInterface
    {
        $data = DB::table('products')
            ->select([
                'products.id',
                'products.sku',
                'products.name',
                'products.price',
                'products.type',
                'dvd_products.size',
                'book_products.weight',
                'furniture_products.height',
                'furniture_products.width',
                'furniture_products.length'
            ])
            ->leftJoin('dvd_products', 'products.id', '=', 'dvd_products.id')
            ->leftJoin('book_products', 'products.id', '=', 'book_products.id')
            ->leftJoin('furniture_products', 'products.id', '=', 'furniture_products.id')
            ->where('products.sku', '=', $sku)
            ->first();

        if (!$data) {
            return null;
        }

        /** @var ProductInterface $product */
        $product = $this->hydrate($data);

        return $product;
    }

    /**
     * Check if a SKU already exists.
     *
     * @param string $sku The SKU to check.
     * @return bool True if the SKU exists.
     */
    public function skuExists(string $sku): bool
    {
        return DB::table('products')
            ->where('sku', '=', $sku)
            ->exists();
    }

    /**
     * Save a new product to the database using CTI pattern.
     *
     * Inserts into the parent products table first, then into the
     * appropriate child table for type-specific attributes.
     *
     * @param ProductInterface $product The product to save.
     * @return ProductInterface The saved product with ID set.
     * @throws DuplicateSkuException If the SKU already exists.
     * @throws \PDOException If a database error occurs.
     */
    public function save(ProductInterface $product): ProductInterface
    {
        // Check for duplicate SKU
        if ($this->skuExists($product->getSku())) {
            throw new DuplicateSkuException(
                "SKU '{$product->getSku()}' already exists."
            );
        }

        $this->beginTransaction();

        try {
            // Insert into parent table
            $productId = DB::table('products')->insertRaw([
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'type' => $product->getType(),
            ]);

            // Get type-specific attributes and child table name
            $specificAttributes = $product->getSpecificAttributesArray();
            $childTableName = $this->getChildTableName($product->getType());

            // Insert into child table with the same ID
            $childData = array_merge(['id' => $productId], $specificAttributes);
            DB::table($childTableName)->insertRaw($childData);

            $this->commit();

            // Set the ID on the product entity
            $product->setId($productId);

            return $product;
        } catch (\PDOException $e) {
            $this->rollBack();

            // Check for duplicate SKU error (MySQL error code 1062)
            if (strpos($e->getMessage(), 'Duplicate entry') !== false &&
                strpos($e->getMessage(), 'sku') !== false) {
                throw new DuplicateSkuException(
                    "SKU '{$product->getSku()}' already exists."
                );
            }

            throw $e;
        }
    }

    /**
     * Delete a product by ID.
     *
     * Deletes from the parent table (child records are deleted by CASCADE).
     *
     * @param int $id The product ID.
     * @return int The number of deleted rows.
     */
    public function deleteById(int $id): int
    {
        return DB::table('products')
            ->where('id', '=', $id)
            ->delete();
    }

    /**
     * Delete multiple products by their SKUs.
     *
     * @param array<int, string> $skus The SKUs to delete.
     * @return int The number of deleted rows.
     */
    public function deleteBySkus(array $skus): int
    {
        if (empty($skus)) {
            return 0;
        }

        return DB::table('products')
            ->whereIn('sku', $skus)
            ->delete();
    }

    /**
     * Delete multiple products by their IDs.
     *
     * @param array<int, int> $ids The product IDs to delete.
     * @return int The number of deleted rows.
     */
    public function deleteByIds(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        return DB::table('products')
            ->whereIn('id', $ids)
            ->delete();
    }

    /**
     * Get the child table name for a product type.
     *
     * @param string $type The product type.
     * @return string The child table name.
     */
    private function getChildTableName(string $type): string
    {
        // Use a mapping to avoid conditionals in business logic
        $tableMap = [
            'dvd' => 'dvd_products',
            'book' => 'book_products',
            'furniture' => 'furniture_products',
        ];

        if (!isset($tableMap[$type])) {
            throw new \InvalidArgumentException("Unknown product type: {$type}");
        }

        return $tableMap[$type];
    }
}
