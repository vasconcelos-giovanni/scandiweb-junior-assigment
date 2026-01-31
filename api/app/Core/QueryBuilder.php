<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

/**
 * Fluent SQL Query Builder.
 *
 * Provides a fluent interface for building and executing SQL queries.
 * Supports SELECT, INSERT, UPDATE, DELETE operations with WHERE clauses,
 * JOINs, ORDER BY, LIMIT, and OFFSET.
 *
 * @example
 * // Select all products
 * $products = DB::table('products')->get();
 *
 * // Find by condition with join
 * $product = DB::table('products')
 *     ->select(['products.*', 'dvd_products.size'])
 *     ->leftJoin('dvd_products', 'products.id', '=', 'dvd_products.id')
 *     ->where('products.sku', '=', 'ABC123')
 *     ->first();
 *
 * // Insert new record
 * $id = DB::table('products')->insert([
 *     'sku' => 'XYZ789',
 *     'name' => 'Test Product',
 *     'price' => 29.99,
 *     'type' => 'dvd'
 * ]);
 */
class QueryBuilder
{
    /** @var PDO */
    private PDO $connection;

    /** @var string */
    private string $table;

    /** @var array<int, string> */
    private array $columns = ['*'];

    /** @var array<int, array<string, mixed>> */
    private array $wheres = [];

    /** @var array<int, mixed> */
    private array $bindings = [];

    /** @var array<int, array<string, string>> */
    private array $joins = [];

    /** @var array<int, array<string, string>> */
    private array $orderBy = [];

    /** @var int|null */
    private ?int $limit = null;

    /** @var int|null */
    private ?int $offset = null;

    /**
     * Create a new QueryBuilder instance.
     *
     * @param PDO $connection The PDO database connection.
     * @param string $table The table to query.
     */
    public function __construct(PDO $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Set the table for the query.
     *
     * @param string $table The table name.
     * @return self
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set the columns to select.
     *
     * @param array<int, string> $columns The columns to select.
     * @return self
     */
    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add a basic WHERE clause.
     *
     * @param string $column The column name.
     * @param string $operator The comparison operator (=, <, >, <=, >=, <>, LIKE).
     * @param mixed $value The value to compare against.
     * @return self
     */
    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add a WHERE IN clause.
     *
     * @param string $column The column name.
     * @param array<int, mixed> $values The values to match against.
     * @return self
     */
    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values
        ];
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    /**
     * Add a WHERE NULL clause.
     *
     * @param string $column The column name.
     * @return self
     */
    public function whereNull(string $column): self
    {
        $this->wheres[] = [
            'type' => 'null',
            'column' => $column
        ];
        return $this;
    }

    /**
     * Add a WHERE NOT NULL clause.
     *
     * @param string $column The column name.
     * @return self
     */
    public function whereNotNull(string $column): self
    {
        $this->wheres[] = [
            'type' => 'notNull',
            'column' => $column
        ];
        return $this;
    }

    /**
     * Add an INNER JOIN clause.
     *
     * @param string $table The table to join.
     * @param string $first The first column (from main table).
     * @param string $operator The join operator.
     * @param string $second The second column (from joined table).
     * @return self
     */
    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = [
            'type' => 'INNER',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];
        return $this;
    }

    /**
     * Add a LEFT JOIN clause.
     *
     * @param string $table The table to join.
     * @param string $first The first column (from main table).
     * @param string $operator The join operator.
     * @param string $second The second column (from joined table).
     * @return self
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = [
            'type' => 'LEFT',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];
        return $this;
    }

    /**
     * Add a RIGHT JOIN clause.
     *
     * @param string $table The table to join.
     * @param string $first The first column (from main table).
     * @param string $operator The join operator.
     * @param string $second The second column (from joined table).
     * @return self
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = [
            'type' => 'RIGHT',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];
        return $this;
    }

    /**
     * Add an ORDER BY clause.
     *
     * @param string $column The column to order by.
     * @param string $direction The sort direction (ASC or DESC).
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }
        $this->orderBy[] = [
            'column' => $column,
            'direction' => $direction
        ];
        return $this;
    }

    /**
     * Set the LIMIT clause.
     *
     * @param int $limit The maximum number of rows to return.
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the OFFSET clause.
     *
     * @param int $offset The number of rows to skip.
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute the query and return all results.
     *
     * @return array<int, array<string, mixed>> The query results.
     */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        return $this->execute($sql)->fetchAll();
    }

    /**
     * Execute the query and return the first result.
     *
     * @return array<string, mixed>|null The first result or null if not found.
     */
    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

    /**
     * Check if any records exist matching the query.
     *
     * @return bool True if records exist.
     */
    public function exists(): bool
    {
        return $this->first() !== null;
    }

    /**
     * Get the count of records matching the query.
     *
     * @return int The count of matching records.
     */
    public function count(): int
    {
        $originalColumns = $this->columns;
        $this->columns = ['COUNT(*) as aggregate'];
        $result = $this->first();
        $this->columns = $originalColumns;
        return (int)($result['aggregate'] ?? 0);
    }

    /**
     * Insert a new record and return the inserted ID.
     *
     * @param array<string, mixed> $data The data to insert.
     * @return int The last inserted ID.
     */
    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->bindings = array_values($data);
        $this->execute($sql);
        return (int)$this->connection->lastInsertId();
    }

    /**
     * Insert a record and return the last insert ID without auto-transaction.
     * Useful when you want to manage transactions externally.
     *
     * @param array<string, mixed> $data The data to insert.
     * @return int The last inserted ID.
     */
    public function insertRaw(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
        return (int)$this->connection->lastInsertId();
    }

    /**
     * Update records matching the WHERE clause.
     *
     * @param array<string, mixed> $data The data to update.
     * @return int The number of affected rows.
     */
    public function update(array $data): int
    {
        $updateBindings = array_values($data);
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = ?";
        }

        $sql = sprintf(
            "UPDATE %s SET %s",
            $this->table,
            implode(', ', $setParts)
        );

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        // Merge update bindings with where bindings
        $allBindings = array_merge($updateBindings, $this->bindings);
        $this->bindings = $allBindings;

        return $this->execute($sql)->rowCount();
    }

    /**
     * Delete records matching the WHERE clause.
     *
     * @return int The number of deleted rows.
     */
    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        return $this->execute($sql)->rowCount();
    }

    /**
     * Build the SELECT query string.
     *
     * @return string The SQL query.
     */
    private function buildSelectQuery(): string
    {
        $columns = implode(', ', $this->columns);
        $sql = "SELECT {$columns} FROM {$this->table}";

        // Add JOINs
        foreach ($this->joins as $join) {
            $sql .= sprintf(
                " %s JOIN %s ON %s %s %s",
                $join['type'],
                $join['table'],
                $join['first'],
                $join['operator'],
                $join['second']
            );
        }

        // Add WHERE clause
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        // Add ORDER BY
        if (!empty($this->orderBy)) {
            $orderParts = [];
            foreach ($this->orderBy as $order) {
                $orderParts[] = "{$order['column']} {$order['direction']}";
            }
            $sql .= " ORDER BY " . implode(', ', $orderParts);
        }

        // Add LIMIT
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        // Add OFFSET
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    /**
     * Build the WHERE clause string.
     *
     * @return string The WHERE clause.
     */
    private function buildWhereClause(): string
    {
        $clauses = [];

        foreach ($this->wheres as $where) {
            switch ($where['type']) {
                case 'basic':
                    $clauses[] = "{$where['column']} {$where['operator']} ?";
                    break;
                case 'in':
                    $placeholders = implode(', ', array_fill(0, count($where['values']), '?'));
                    $clauses[] = "{$where['column']} IN ({$placeholders})";
                    break;
                case 'null':
                    $clauses[] = "{$where['column']} IS NULL";
                    break;
                case 'notNull':
                    $clauses[] = "{$where['column']} IS NOT NULL";
                    break;
            }
        }

        return implode(' AND ', $clauses);
    }

    /**
     * Execute a SQL statement with automatic transaction management.
     *
     * @param string $sql The SQL statement to execute.
     * @return PDOStatement The executed statement.
     * @throws \PDOException If the query fails.
     */
    private function execute(string $sql): PDOStatement
    {
        $startedTransaction = !$this->connection->inTransaction();

        if ($startedTransaction) {
            $this->connection->beginTransaction();
        }

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bindings);

            if ($startedTransaction) {
                $this->connection->commit();
            }

            // Reset state for next query
            $this->reset();

            return $stmt;
        } catch (\PDOException $e) {
            if ($startedTransaction) {
                $this->connection->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Reset the query builder state for reuse.
     *
     * @return void
     */
    private function reset(): void
    {
        $this->bindings = [];
        $this->wheres = [];
        $this->joins = [];
        $this->orderBy = [];
        $this->columns = ['*'];
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * Get the underlying PDO connection.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
