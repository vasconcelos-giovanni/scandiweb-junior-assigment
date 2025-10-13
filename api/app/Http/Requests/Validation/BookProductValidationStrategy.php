<?php
declare(strict_types=1);

namespace App\Http\Requests\Validation;

class BookProductValidationStrategy implements ProductValidationStrategyInterface
{
    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['weight'])) {
            $errors['weight'] = 'The weight field is required for Book products.';
        } elseif (!is_numeric($data['weight'])) {
            $errors['weight'] = 'The weight must be a number.';
        }
        return $errors;
    }
}