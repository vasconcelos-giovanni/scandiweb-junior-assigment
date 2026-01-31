<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * DTO for creating DVD products.
 *
 * Handles validation and data transfer for DVD-specific attributes.
 */
class CreateDvdDto extends CreateProductDto
{
    /** @var int The DVD size in MB */
    protected int $size;

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'dvd';
    }

    /**
     * {@inheritDoc}
     */
    protected function validateSpecificFields(array $data): array
    {
        $errors = [];

        if (!isset($data['size']) || $data['size'] === '') {
            $errors['size'] = 'Please, submit required data';
        } elseif (!is_numeric($data['size'])) {
            $errors['size'] = 'Please, provide the data of indicated type';
        } elseif ((int)$data['size'] < 0) {
            $errors['size'] = 'Size cannot be negative';
        }

        return $errors;
    }

    /**
     * {@inheritDoc}
     */
    protected function hydrateSpecificFields(array $data): void
    {
        $this->size = (int)$data['size'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSpecificFieldsArray(): array
    {
        return ['size' => $this->size];
    }

    /**
     * Get the DVD size in MB.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}
