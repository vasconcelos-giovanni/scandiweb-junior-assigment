<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * DTO for creating Book products.
 *
 * Handles validation and data transfer for Book-specific attributes.
 */
class CreateBookDto extends CreateProductDto
{
    /** @var float The book weight in Kg */
    protected float $weight;

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'book';
    }

    /**
     * {@inheritDoc}
     */
    protected function validateSpecificFields(array $data): array
    {
        $errors = [];

        if (!isset($data['weight']) || $data['weight'] === '') {
            $errors['weight'] = 'Please, submit required data';
        } elseif (!is_numeric($data['weight'])) {
            $errors['weight'] = 'Please, provide the data of indicated type';
        } elseif ((float)$data['weight'] < 0) {
            $errors['weight'] = 'Weight cannot be negative';
        }

        return $errors;
    }

    /**
     * {@inheritDoc}
     */
    protected function hydrateSpecificFields(array $data): void
    {
        $this->weight = (float)$data['weight'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSpecificFieldsArray(): array
    {
        return ['weight' => $this->weight];
    }

    /**
     * Get the book weight in Kg.
     *
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }
}
