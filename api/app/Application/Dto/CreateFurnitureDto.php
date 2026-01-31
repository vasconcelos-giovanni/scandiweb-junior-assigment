<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * DTO for creating Furniture products.
 *
 * Handles validation and data transfer for Furniture-specific attributes.
 */
class CreateFurnitureDto extends CreateProductDto
{
    /** @var int The height dimension */
    protected int $height;

    /** @var int The width dimension */
    protected int $width;

    /** @var int The length dimension */
    protected int $length;

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'furniture';
    }

    /**
     * {@inheritDoc}
     */
    protected function validateSpecificFields(array $data): array
    {
        $errors = [];
        $fields = ['height', 'width', 'length'];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[$field] = 'Please, submit required data';
            } elseif (!is_numeric($data[$field])) {
                $errors[$field] = 'Please, provide the data of indicated type';
            } elseif ((int)$data[$field] < 0) {
                $errors[$field] = ucfirst($field) . ' cannot be negative';
            }
        }

        return $errors;
    }

    /**
     * {@inheritDoc}
     */
    protected function hydrateSpecificFields(array $data): void
    {
        $this->height = (int)$data['height'];
        $this->width = (int)$data['width'];
        $this->length = (int)$data['length'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSpecificFieldsArray(): array
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
        ];
    }

    /**
     * Get the height dimension.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the width dimension.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get the length dimension.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }
}
