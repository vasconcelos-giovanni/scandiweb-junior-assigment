<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base interface for Entity Factories.
 *
 * Factories create entity instances from DTOs or raw data arrays.
 * They encapsulate the construction logic, especially useful for
 * entities with complex initialization or polymorphic types.
 *
 * @example
 * class UserFactory implements FactoryInterface
 * {
 *     public function createFromDto(DtoInterface $dto): Entity
 *     {
 *         return new User($dto->getEmail(), $dto->getPassword());
 *     }
 * }
 */
interface FactoryInterface
{
    /**
     * Create an entity from a validated DTO.
     *
     * @param DtoInterface $dto The validated DTO.
     * @return Entity The created entity.
     */
    public function createFromDto(DtoInterface $dto): Entity;

    /**
     * Create an entity from an array of data.
     *
     * @param array<string, mixed> $data The entity data.
     * @return Entity The created entity.
     */
    public function createFromArray(array $data): Entity;
}
