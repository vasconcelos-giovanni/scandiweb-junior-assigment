<?php

declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Domain\Contracts\ProductInterface;

/**
 * Interface for Product hydrators.
 *
 * Hydrators transform database rows into domain entity objects.
 * Each product type has its own hydrator implementation.
 */
interface ProductHydratorInterface
{
    /**
     * Hydrate a database row into a Product entity.
     *
     * @param array<string, mixed> $data The database row data.
     * @return ProductInterface The hydrated product entity.
     */
    public function hydrate(array $data): ProductInterface;
}
