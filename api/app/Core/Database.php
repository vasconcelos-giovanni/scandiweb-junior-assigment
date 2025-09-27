<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect(): void
    {
        $driver = $this->config->get('DB_DRIVER', 'sqlite');
        $host = $this->config->get('DB_HOST', 'localhost');
        $port = $this->config->get('DB_PORT', '3306');
        $database = $this->config->get('DB_DATABASE');
        $username = $this->config->get('DB_USERNAME');
        $password = $this->config->get('DB_PASSWORD');

        try {
            switch ($driver) {
                case 'sqlite':
                    // Ensure the directory exists
                    $databaseDir = dirname($database);
                    if (!is_dir($databaseDir)) {
                        mkdir($databaseDir, 0777, true);
                    }
                    
                    // Create the database file if it doesn't exist
                    if (!file_exists($database)) {
                        touch($database);
                    }
                    
                    $dataSourceName = "sqlite:{$database}";
                    break;
                    
                case 'mysql':
                    $dataSourceName = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
                    break;
                    
                default:
                    throw new \InvalidArgumentException("Unsupported database driver: {$driver}");
            }

            $this->connection = new PDO($dataSourceName, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }
}