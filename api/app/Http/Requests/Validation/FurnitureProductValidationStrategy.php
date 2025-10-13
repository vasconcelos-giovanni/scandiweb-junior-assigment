<?php
declare(strict_types=1);

namespace App\Http\Requests\Validation;

class FurnitureProductValidationStrategy implements ProductValidationStrategyInterface
{
    public function validate(array $data): array
    {
        $errors = [];
        $fields = ['height', 'width', 'length'];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "The {$field} field is required for Furniture products.";
            } elseif (!is_numeric($data[$field])) {
                $errors[$field] = "The {$field} must be a number.";
            }
        }
        return $errors;
    }
}