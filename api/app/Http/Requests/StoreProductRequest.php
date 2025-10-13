<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Core\Request;
use App\Exceptions\ValidationException;
use App\Http\Requests\Validation\ProductValidationStrategyResolver;

class StoreProductRequest
{
    private array $data;
    private array $errors = [];
    private ProductValidationStrategyResolver $resolver;

    public function __construct(Request $request, ProductValidationStrategyResolver $resolver)
    {
        $this->data = $request->getJson() ?? [];
        $this->resolver = $resolver;
    }

    public function validated(): array
    {
        $this->validateRequired(['sku', 'name', 'price', 'type']);
        $this->validateString(['sku', 'name', 'type']);
        $this->validateNumeric(['price']);

        $type = $this->data['type'] ?? null;
        if ($type && is_string($type) && !isset($this->errors['type'])) {
            try {
                $strategy = $this->resolver->resolve($type);
                $specificErrors = $strategy->validate($this->data);
                $this->errors = array_merge($this->errors, $specificErrors);
            } catch (\InvalidArgumentException $e) {
                $this->errors['type'] = 'Invalid product type specified.';
            }
        }
        
        if (!empty($this->errors)) {
            throw new ValidationException($this->errors);
        }

        return $this->data;
    }

    private function validateRequired(array $fields): void {/*...*/}
    private function validateString(array $fields): void {/*...*/}
    private function validateNumeric(array $fields): void {/*...*/}
}