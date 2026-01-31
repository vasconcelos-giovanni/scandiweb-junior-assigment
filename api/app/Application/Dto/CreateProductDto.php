<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Exceptions\ValidationException;

/**
 * Abstract base class for product creation DTOs.
 *
 * Provides common validation logic and property handling
 * for all product types. Type-specific DTOs extend this class
 * to add their own validation rules through polymorphism.
 *
 * This pattern avoids conditional statements for type differences
 * as required by Scandiweb guidelines.
 */
abstract class CreateProductDto implements CreateProductDtoInterface
{
    /** @var string */
    protected string $sku;

    /** @var string */
    protected string $name;

    /** @var float */
    protected float $price;

    /**
     * Protected constructor - use fromArray() factory method.
     */
    protected function __construct()
    {
        // Use static factory method
    }

    /**
     * {@inheritDoc}
     */
    abstract public static function getType(): string;

    /**
     * Validate type-specific fields.
     *
     * @param array<string, mixed> $data The input data.
     * @return array<string, string> Validation errors (field => message).
     */
    abstract protected function validateSpecificFields(array $data): array;

    /**
     * Hydrate type-specific fields from data.
     *
     * @param array<string, mixed> $data The input data.
     * @return void
     */
    abstract protected function hydrateSpecificFields(array $data): void;

    /**
     * Get type-specific fields as an array.
     *
     * @return array<string, mixed>
     */
    abstract protected function getSpecificFieldsArray(): array;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): CreateProductDtoInterface
    {
        $dto = new static();
        $dto->validate($data);
        $dto->hydrate($data);
        return $dto;
    }

    /**
     * Validate all fields including common and type-specific.
     *
     * @param array<string, mixed> $data The input data.
     * @return void
     * @throws ValidationException If validation fails.
     */
    protected function validate(array $data): void
    {
        $errors = [];

        // Validate common fields
        $errors = array_merge($errors, $this->validateCommonFields($data));

        // Validate type-specific fields (polymorphism - no conditionals!)
        $errors = array_merge($errors, $this->validateSpecificFields($data));

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Validate common product fields.
     *
     * @param array<string, mixed> $data The input data.
     * @return array<string, string> Validation errors.
     */
    protected function validateCommonFields(array $data): array
    {
        $errors = [];

        // SKU validation
        if (!isset($data['sku']) || trim((string)$data['sku']) === '') {
            $errors['sku'] = 'Please, submit required data';
        } elseif (!is_string($data['sku'])) {
            $errors['sku'] = 'Please, provide the data of indicated type';
        }

        // Name validation
        if (!isset($data['name']) || trim((string)$data['name']) === '') {
            $errors['name'] = 'Please, submit required data';
        } elseif (!is_string($data['name'])) {
            $errors['name'] = 'Please, provide the data of indicated type';
        }

        // Price validation
        if (!isset($data['price']) || $data['price'] === '') {
            $errors['price'] = 'Please, submit required data';
        } elseif (!is_numeric($data['price'])) {
            $errors['price'] = 'Please, provide the data of indicated type';
        } elseif ((float)$data['price'] < 0) {
            $errors['price'] = 'Price cannot be negative';
        }

        return $errors;
    }

    /**
     * Hydrate the DTO from validated data.
     *
     * @param array<string, mixed> $data The validated data.
     * @return void
     */
    protected function hydrate(array $data): void
    {
        $this->sku = trim((string)$data['sku']);
        $this->name = trim((string)$data['name']);
        $this->price = (float)$data['price'];

        // Hydrate type-specific fields (polymorphism)
        $this->hydrateSpecificFields($data);
    }

    // =========================================================================
    // GETTERS
    // =========================================================================

    /**
     * {@inheritDoc}
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'sku' => $this->sku,
                'name' => $this->name,
                'price' => $this->price,
                'type' => static::getType(),
            ],
            $this->getSpecificFieldsArray()
        );
    }
}
