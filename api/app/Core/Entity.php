<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;

/**
 * The base Entity class for all domain entities.
 */
abstract class Entity
{
    /** @var AnnotationParser|null */
    private static $parser = null;

    /**
     * Lazily initialize and return the AnnotationParser.
     */
    private static function getAnnotationParser(): AnnotationParser
    {
        if (self::$parser === null) {
            self::$parser = new AnnotationParser();
        }
        return self::$parser;
    }

    /**
     * Retrieves a specific annotation block for the current class.
     * @return array<string, string>|null
     */
    private static function getAnnotation(string $annotationName): ?array
    {
        try {
            $reflection = new ReflectionClass(static::class);
            $docComment = $reflection->getDocComment();
            if (!$docComment) {
                return null;
            }
            $annotations = self::getAnnotationParser()->parse($docComment);
            return $annotations[$annotationName] ?? null;
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * @return string The name of the table for this entity, read from @Table.
     */
    public static function getTableName(): string
    {
        $annotation = self::getAnnotation('Table');
        if (isset($annotation['name'])) {
            return $annotation['name'];
        }
        $message = 'Entity ' . static::class
            . ' is missing the required @Table(name="...") annotation.';
        throw new \LogicException($message);
    }

    /**
     * The name of the column used to differentiate between child classes.
     * Read from @DiscriminatorColumn in the parent class.
     */
    public static function getDiscriminatorColumn(): ?string
    {
        $annotation = self::getAnnotation('DiscriminatorColumn');
        return $annotation['name'] ?? null;
    }

    /**
     * Returns the map of discriminator values to their corresponding child class names.
     * Read from @DiscriminatorMap in the parent class.
     * @return array<string, string> The discriminator map.
     */
    public static function getDiscriminatorMap(): array
    {
        $annotation = self::getAnnotation('DiscriminatorMap');
        if (!$annotation) {
            return [];
        }

        // Preferred style: @DiscriminatorMap(dvd="App\\Entities\\DvdProduct", book="...")
        // In this case, the annotation array already matches the desired map shape.
        if (!isset($annotation['map'])) {
            // Normalize FQCN backslashes
            $map = [];
            foreach ($annotation as $key => $value) {
                $map[$key] = str_replace('\\\\', '\\', $value);
            }
            return $map; // keys are discriminator values, values are FQCNs
        }

        // Backward-compat style: @DiscriminatorMap(map='"dvd":"App\\Entities\\DvdProduct"')
        $inner = $annotation['map'];
        $json = '{' . $inner . '}';
        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            foreach ($decoded as $k => $v) {
                $decoded[$k] = str_replace('\\\\', '\\', $v);
            }
            return $decoded;
        }
        return [];
    }
}
