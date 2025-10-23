<?php
declare(strict_types=1);

namespace App\Core;

/**
 * The base Entity class for all domain entities.
 *
 * This abstract class establishes the contract for how entities provide their
 * database schema information, enabling automatic migration generation via Reflection.
 */
abstract class Entity
{
    /**
     * @return string The name of the table for this entity.
     */
    abstract public static function getTableName(): string;

    /**
     * The name of the column used to differentiate between child classes
     * in an inheritance hierarchy (e.g., 'type').
     * 
     * Override this method only in the PARENT class of an inheritance setup.
     */
    public static function getDiscriminatorColumn(): ?string
    {
        return null;
    }

    /**
     * Returns the map of discriminator values to their corresponding child class names.
     * E.g.: ['dvd' => DvdProduct::class]
     *
     * Override this method only in the PARENT class of an inheritance setup.
     *
     * @return array<string, string> The discriminator map.
     */
    public static function getDiscriminatorMap(): array
    {
        return [];
    }
}