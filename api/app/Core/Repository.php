<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Abstract base Repository class.
 *
 * Provides common database operations for entities following the Repository pattern.
 * Extend this class to create entity-specific repositories with custom query methods.
 *
 * This class is inspired by Doctrine's EntityRepository and Symfony's approach to
 * data access, providing a clean abstraction layer between domain entities and
 * the database.
 *
 * @example
 * class UserRepository extends Repository
 * {
 *     protected function getEntityClass(): string
 *     {
 *         return User::class;
 *     }
 *
 *     public function findByEmail(string $email): ?User
 *     {
 *         $data = $this->query()
 *             ->where('email', '=', $email)
 *             ->first();
 *         return $data ? $this->hydrate($data) : null;
 *     }
 * }
 */
abstract class Repository
{
    /** @var Database */
    protected Database $db;

    /**
     * Create a new Repository instance.
     *
     * @param Database $db The database connection.
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get the fully qualified class name of the entity this repository manages.
     *
     * @return string The entity class name.
     */
    abstract protected function getEntityClass(): string;

    /**
     * Hydrate a database row into an entity instance.
     *
     * @param array<string, mixed> $data The database row data.
     * @return Entity The hydrated entity.
     */
    abstract protected function hydrate(array $data): Entity;

    /**
     * Get the table name for this repository's entity.
     *
     * @return string The table name.
     */
    protected function getTableName(): string
    {
        $entityClass = $this->getEntityClass();
        return $entityClass::getTableName();
    }

    /**
     * Create a new QueryBuilder instance for this repository's table.
     *
     * @return QueryBuilder
     */
    protected function query(): QueryBuilder
    {
        return DB::table($this->getTableName());
    }

    /**
     * Find an entity by its primary key ID.
     *
     * @param int $id The entity ID.
     * @return Entity|null The entity or null if not found.
     */
    public function find(int $id): ?Entity
    {
        $data = $this->query()
            ->where('id', '=', $id)
            ->first();

        return $data ? $this->hydrate($data) : null;
    }

    /**
     * Find all entities.
     *
     * @return array<int, Entity> Array of entities.
     */
    public function findAll(): array
    {
        $results = $this->query()->get();

        return array_map(function (array $data): Entity {
            return $this->hydrate($data);
        }, $results);
    }

    /**
     * Find entities by a specific field value.
     *
     * @param string $field The field name.
     * @param mixed $value The value to match.
     * @return array<int, Entity> Array of matching entities.
     */
    public function findBy(string $field, $value): array
    {
        $results = $this->query()
            ->where($field, '=', $value)
            ->get();

        return array_map(function (array $data): Entity {
            return $this->hydrate($data);
        }, $results);
    }

    /**
     * Find a single entity by a specific field value.
     *
     * @param string $field The field name.
     * @param mixed $value The value to match.
     * @return Entity|null The entity or null if not found.
     */
    public function findOneBy(string $field, $value): ?Entity
    {
        $data = $this->query()
            ->where($field, '=', $value)
            ->first();

        return $data ? $this->hydrate($data) : null;
    }

    /**
     * Check if an entity exists with the given field value.
     *
     * @param string $field The field name.
     * @param mixed $value The value to check.
     * @return bool True if exists.
     */
    public function exists(string $field, $value): bool
    {
        return $this->query()
            ->where($field, '=', $value)
            ->exists();
    }

    /**
     * Count all entities.
     *
     * @return int The total count.
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * Delete an entity by ID.
     *
     * @param int $id The entity ID.
     * @return int The number of deleted rows.
     */
    public function delete(int $id): int
    {
        return $this->query()
            ->where('id', '=', $id)
            ->delete();
    }

    /**
     * Delete multiple entities by their IDs.
     *
     * @param array<int, int> $ids The entity IDs.
     * @return int The number of deleted rows.
     */
    public function deleteByIds(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        return $this->query()
            ->whereIn('id', $ids)
            ->delete();
    }

    /**
     * Get the underlying database connection.
     *
     * @return Database
     */
    protected function getDatabase(): Database
    {
        return $this->db;
    }

    /**
     * Begin a database transaction.
     *
     * @return bool
     */
    protected function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit the current transaction.
     *
     * @return bool
     */
    protected function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback the current transaction.
     *
     * @return bool
     */
    protected function rollBack(): bool
    {
        return $this->db->rollBack();
    }
}
