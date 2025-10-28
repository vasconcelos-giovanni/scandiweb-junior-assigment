<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

class QueryBuilder
{
    private PDO $connection;
    private string $table;
    private array $columns = ['*'];
    private array $wheres = [];
    private array $bindings = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(PDO $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = ['type' => 'basic', 'column' => $column, 'operator' => $operator, 'value' => $value];
        $this->bindings[] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = ['type' => 'in', 'column' => $column, 'values' => $values];
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        return $this->executeWithTransaction($sql)->fetchAll();
    }

    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

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
        $this->executeWithTransaction($sql);
        return (int)$this->connection->lastInsertId();
    }

    public function update(array $data): int
    {
        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $this->bindings[] = $value;
        }

        $sql = sprintf(
            "UPDATE %s SET %s",
            $this->table,
            implode(', ', $setParts)
        );

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        return $this->executeWithTransaction($sql)->rowCount();
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        return $this->executeWithTransaction($sql)->rowCount();
    }

    private function buildSelectQuery(): string
    {
        $columns = implode(', ', $this->columns);
        $sql = "SELECT {$columns} FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    private function buildWhereClause(): string
    {
        $clauses = [];

        foreach ($this->wheres as $where) {
            if ($where['type'] === 'basic') {
                $clauses[] = "{$where['column']} {$where['operator']} ?";
            } elseif ($where['type'] === 'in') {
                $placeholders = implode(', ', array_fill(0, count($where['values']), '?'));
                $clauses[] = "{$where['column']} IN ({$placeholders})";
            }
        }

        return implode(' AND ', $clauses);
    }

    private function executeWithTransaction(string $sql): PDOStatement
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

            // Reset bindings for next query
            $this->bindings = [];

            return $stmt;
        } catch (\Exception $e) {
            if ($startedTransaction) {
                $this->connection->rollBack();
            }
            throw $e;
        }
    }
}
