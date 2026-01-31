<?php

declare(strict_types=1);

namespace App\Infrastructure\Hydrators;

use App\Core\HydratorInterface;

/**
 * Interface for Product hydrators.
 *
 * Hydrators transform database rows into domain entity objects.
 * Each product type has its own hydrator implementation.
 *
 * Implementations should return ProductInterface instances.
 */
interface ProductHydratorInterface extends HydratorInterface
{
}
