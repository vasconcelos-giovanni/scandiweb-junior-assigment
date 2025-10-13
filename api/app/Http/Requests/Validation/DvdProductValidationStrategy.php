<?php
declare(strict_types=1);

namespace App\Http\Requests\Validation;

class DvdProductValidationStrategy implements ProductValidationStrategyInterface
{
    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['size'])) {
            $errors['size'] = 'The size field is required for DVD products.';
        } elseif (!is_numeric($data['size'])) {
            $errors['size'] = 'The size must be a number.';
        }
        return $errors;
    }
}