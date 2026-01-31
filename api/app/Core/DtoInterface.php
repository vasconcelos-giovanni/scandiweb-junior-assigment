<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base interface for Data Transfer Objects.
 *
 * DTOs are used to transfer validated data between layers.
 * They ensure data integrity before entities are created.
 *
 * @example
 * interface CreateUserDtoInterface extends DtoInterface
 * {
 *     public function getEmail(): string;
 *     public function getPassword(): string;
 * }
 */
interface DtoInterface
{
    /**
     * Create a DTO instance from an array of data.
     *
     * This method should validate the data and throw
     * ValidationException if validation fails.
     *
     * @param array<string, mixed> $data The input data.
     * @return static The validated DTO instance.
     * @throws \App\Exceptions\ValidationException If validation fails.
     */
    public static function fromArray(array $data): self;

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed> The DTO data.
     */
    public function toArray(): array;
}
