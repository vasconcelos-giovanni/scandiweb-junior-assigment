<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base interface for Entity Hydrators.
 *
 * Hydrators transform database rows into domain entity objects.
 * They handle the mapping between database column names and
 * entity properties, including type casting.
 *
 * Use hydrators when:
 * - You have complex entity construction from DB data
 * - You need to handle polymorphic entities (CTI/STI)
 * - You want to separate persistence mapping from entity logic
 *
 * @example
 * class UserHydrator implements HydratorInterface
 * {
 *     public function hydrate(array $data): Entity
 *     {
 *         $user = new User();
 *         $user->setId((int)$data['id']);
 *         $user->setEmail((string)$data['email']);
 *         return $user;
 *     }
 * }
 */
interface HydratorInterface
{
    /**
     * Hydrate a database row into an Entity.
     *
     * @param array<string, mixed> $data The database row data.
     * @return Entity The hydrated entity.
     */
    public function hydrate(array $data): Entity;
}
