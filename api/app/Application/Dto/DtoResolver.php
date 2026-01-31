<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Core\Container;
use App\Exceptions\ValidationException;

/**
 * Resolves the appropriate DTO class based on product type.
 *
 * Uses the Container to lookup registered DTO classes,
 * avoiding conditional statements for type handling as required
 * by Scandiweb guidelines.
 */
class DtoResolver
{
    /** @var Container */
    private Container $container;

    /**
     * Create a new DtoResolver instance.
     *
     * @param Container $container The DI container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve and create a DTO instance from request data.
     *
     * @param array<string, mixed> $data The request data (must contain 'type').
     * @return CreateProductDtoInterface The validated DTO.
     * @throws ValidationException If type is missing or invalid.
     */
    public function resolve(array $data): CreateProductDtoInterface
    {
        // Validate type exists
        if (!isset($data['type']) || trim((string)$data['type']) === '') {
            throw new ValidationException(['type' => 'Please, submit required data']);
        }

        $type = strtolower(trim((string)$data['type']));
        $key = "product.dto.{$type}";

        if (!$this->container->has($key)) {
            throw new ValidationException(['type' => 'Invalid product type specified']);
        }

        // Get the DTO class from container and create instance
        /** @var string $dtoClass */
        $dtoClass = $this->container->make($key);

        // Validate the class exists and implements the interface
        if (!class_exists($dtoClass)) {
            throw new ValidationException(['type' => 'Invalid product type configuration']);
        }

        // Create DTO using static factory method
        return $dtoClass::fromArray($data);
    }

    /**
     * Check if a product type is registered.
     *
     * @param string $type The product type.
     * @return bool True if the type is registered.
     */
    public function hasType(string $type): bool
    {
        $key = "product.dto." . strtolower($type);
        return $this->container->has($key);
    }
}
