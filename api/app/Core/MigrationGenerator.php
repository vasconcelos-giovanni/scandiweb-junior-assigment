<?php
declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use App\Core\Entity;

/**
 * Generates SQL schema from Entity class definitions.
 * It uses Reflection to read annotations and discover the database structure dynamically.
 */
class MigrationGenerator
{
    private AnnotationParser $parser;

    public function __construct()
    {
        $this->parser = new AnnotationParser();
    }

    /**
     * Generates the full SQL schema for a root entity and its children.
     * This version discovers the primary key from annotations.
     *
     * @param string $entityClass The fully qualified class name of the entity.
     * @param bool $isChild Internal flag for recursive calls.
     * @param string|null $primaryKeyName The name of the primary key, passed down to children.
     * @return string The generated SQL script.
     */
    public function generate(string $entityClass, bool $isChild = false, ?string $primaryKeyName = null): string
    {
        if (!is_subclass_of($entityClass, Entity::class)) {
            throw new \InvalidArgumentException("Class {$entityClass} must extend " . Entity::class);
        }

        $reflection = new ReflectionClass($entityClass);
        $tableName = $entityClass::getTableName();
        $columnsSql = [];

        // If this is the root of the hierarchy, we must find its primary key first.
        if (!$isChild) {
            $primaryKeyName = $this->findPrimaryKeyName($reflection);
        }
        
        // A child entity requires a primary key name from its parent.
        if ($isChild && $primaryKeyName === null) {
            throw new \LogicException("Child entity {$entityClass} must inherit a primary key name from its parent.");
        }

        // For child tables, the primary key column also acts as the foreign key.
        if ($isChild) {
            // We assume the child's key is an INT, as it references an auto-incrementing parent key.
            $columnsSql[] = "    `{$primaryKeyName}` INT PRIMARY KEY";
        }

        // Iterate over properties declared *only* in the current class to avoid duplication.
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED) as $property) {
            if ($property->getDeclaringClass()->getName() !== $entityClass) {
                continue; // Skip inherited properties.
            }
            
            $docComment = $property->getDocComment();
            if ($docComment && strpos($docComment, '@Column') !== false) {
                $annotations = $this->parser->parse($docComment);
                if (isset($annotations['Column'])) {
                    $columnName = $property->getName();
                    $type = $annotations['Column']['type'] ?? 'VARCHAR(255)';
                    $options = $annotations['Column']['options'] ?? '';
                    $columnsSql[] = "    `{$columnName}` {$type} {$options}";
                }
            }
        }
        
        // Add the discriminator column for parent tables that have a map.
        if (!$isChild && ($discriminatorColumn = $entityClass::getDiscriminatorColumn())) {
            $types = array_keys($entityClass::getDiscriminatorMap());
            $enumDef = "ENUM('" . implode("', '", $types) . "') NOT NULL";
            $columnsSql[] = "    `{$discriminatorColumn}` {$enumDef}";
        }
        
        // Add the foreign key constraint for child tables, using the discovered PK name.
        if ($isChild) {
            $parentClassName = $reflection->getParentClass()->getName();
            $parentTableName = $parentClassName::getTableName();
            $columnsSql[] = "    FOREIGN KEY (`{$primaryKeyName}`) REFERENCES `{$parentTableName}`(`{$primaryKeyName}`) ON DELETE CASCADE";
        }

        // Assemble the final CREATE TABLE statement.
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (\n";
        $sql .= implode(",\n", $columnsSql);
        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

        // Recursively generate schemas for children, passing along the primary key name.
        if (!$isChild) {
            foreach ($entityClass::getDiscriminatorMap() as $childClass) {
                $sql .= $this->generate($childClass, true, $primaryKeyName);
            }
        }

        return $sql;
    }

    /**
     * Scans an entity's properties via Reflection to find the one marked as 'PRIMARY KEY'.
     *
     * @param ReflectionClass $reflection The reflection of the entity to scan.
     * @return string The name of the primary key property.
     * @throws \LogicException If no property is marked as a primary key.
     */
    private function findPrimaryKeyName(ReflectionClass $reflection): string
    {
        foreach ($reflection->getProperties() as $property) {
            $docComment = $property->getDocComment();
            if ($docComment) {
                $annotations = $this->parser->parse($docComment);
                if (isset($annotations['Column']['options']) && strpos(strtoupper($annotations['Column']['options']), 'PRIMARY KEY') !== false) {
                    return $property->getName(); // Found it!
                }
            }
        }

        throw new \LogicException("No property with 'PRIMARY KEY' option found in entity " . $reflection->getName());
    }
}