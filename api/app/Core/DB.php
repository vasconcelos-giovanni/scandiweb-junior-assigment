<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use App\Core\Database;

class DB
{
    private static ?Database $database = null;

    public static function setDatabase(Database $database): void
    {
        self::$database = $database;
    }

    public static function getConnection(): PDO
    {
        if (self::$database === null) {
            throw new \RuntimeException('Database not set. Call DB::setDatabase() first.');
        }
        return self::$database->getConnection();
    }

    public static function table(string $table): QueryBuilder
    {
        return new QueryBuilder(self::getConnection(), $table);
    }

    public static function raw(string $sql, array $params = [])
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }
}
