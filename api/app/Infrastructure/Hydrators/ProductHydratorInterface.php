<?php
declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Domain\Models\ProductInterface;

interface ProductHydratorInterface
{
    public function hydrate(array $data): ProductInterface;
}