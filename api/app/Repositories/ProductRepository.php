<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Domain\Models\ProductInterface;
use App\Infrastructure\Hydrators\ProductHydratorRegistry;

class ProductRepository
{
    private Database $db;
    private ProductHydratorRegistry $hydratorRegistry;

    public function __construct(Database $db, ProductHydratorRegistry $hydratorRegistry)
    {
        $this->db = $db;
        $this->hydratorRegistry = $hydratorRegistry;
    }

    /**
     * @return ProductInterface[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT 
                p.id, p.sku, p.name, p.price, p.type,
                d.size,
                b.weight,
                f.height, f.width, f.length
            FROM products p
            LEFT JOIN products_dvd d ON p.id = d.product_id
            LEFT JOIN products_book b ON p.id = b.product_id
            LEFT JOIN products_furniture f ON p.id = f.product_id
            ORDER BY p.id ASC
        ";

        $stmt = $this->db->query($sql);
        $productsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);       

        $products = [];
        foreach ($productsData as $data) {
            $hydrator = $this->hydratorRegistry->get($data['type']);
            $products[] = $hydrator->hydrate($data);
        }

        return $products;
    }

    public function save(ProductInterface $product): void
    {
        $this->db->beginTransaction();

        try {
            $sqlProducts = "INSERT INTO products (sku, name, price, type) VALUES (?, ?, ?, ?)";
            $this->db->query($sqlProducts, [
                
                $product->getSku(),
                $product->getName(),
                $product->getPrice(),
                $product->getType()
            ]);          

            $specificAttributes = $product->getSpecificAttributesArray();
            $columns = array_keys($specificAttributes);
            $values = array_values($specificAttributes);
            
            $placeholders = implode(', ', array_fill(0, count($values), '?'));
            $tableName = 'products_' . $product->getType();

            $sqlSpecific = "INSERT INTO {$tableName} (product_id, " . implode(', ', $columns) . ") VALUES (?," . $placeholders . ")";

            $this->db->query($sqlSpecific, array_merge([$product->getId()], $values));

            // If all goes well, commit the transaction
            $this->db->commit();
        } catch (\Exception $e) {
            // If anything fails, roll back the entire transaction
            $this->db->rollBack();
            // Re-throw the exception to be handled by a higher layer
            throw $e;
        }
    }

    public function deleteBySkus(array $skus): int
    {
        if (empty($skus)) {
            return 0;
        }
        
        $placeholders = implode(',', array_fill(0, count($skus), '?'));
        $sql = "DELETE FROM products WHERE sku IN ($placeholders)";
        
        $stmt = $this->db->query($sql, $skus);

        return $stmt->rowCount();
    }
    
    public function findBySku(string $sku): ?array
    {
        $stmt = $this->db->query("SELECT sku FROM products WHERE sku = ?", [$sku]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}