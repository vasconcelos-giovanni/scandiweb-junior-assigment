<?php
declare(strict_types=1);

namespace App\Http\Requests\Validation;

interface ProductValidationStrategyInterface
{
    /**
     * @param array $data The input data to validate.
     * @return array An array of error messages, keyed by field name.
     */
    public function validate(array $data): array;
}