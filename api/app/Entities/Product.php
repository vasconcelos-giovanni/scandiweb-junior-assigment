<?php
declare(strict_types=1);

namespace App\Entities;

use App\Core\Entity;

/**
 * The base class for all product types in the domain.
 * It serves as the parent in a Class Table Inheritance (CTI) hierarchy.
 * 
 * @Table(name="products")
 * @DiscriminatorColumn(name="type", type="ENUM")
 * @DiscriminatorMap(map={"dvd":"App\Entities\DvdProduct"}
 */
abstract class Product extends Entity
{
    /**
     * @Column(type="INT", options="PRIMARY KEY AUTO_INCREMENT")
     */
    protected int $id;

    /**
     * @Column(type="VARCHAR(255)", options="NOT NULL UNIQUE")
     */
    protected string $sku;

    /**
     * @Column(type="VARCHAR(255)", options="NOT NULL")
     */
    protected string $name;

    /**
     * @Column(type="DECIMAL(10, 2)", options="NOT NULL")
     */
    protected float $price;

    // --- Getters ---

    public function getId(): int { return $this->id; }
    public function getSku(): string { return $this->sku; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    
    // --- Setters ---

    /**
     * Sets the ID. This is typically only used by the repository/hydrator
     * after fetching the entity from the database.
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setSku(string $sku): self
    {
        // You could add validation here, e.g., check length or format.
        $this->sku = $sku;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPrice(float $price): self
    {
        // Example of validation within a setter.
        if ($price < 0) {
            throw new \InvalidArgumentException("Price cannot be negative.");
        }
        $this->price = $price;
        return $this;
    }

    // --- Utility Methods ---

    public function toArray(): array
    {
        $data = [];
        $reflection = new \ReflectionObject($this);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($this);
        }
        
        // --- THIS IS THE NEW DYNAMIC PART ---
        // 1. Get the map: ['dvd' => DvdProduct::class, ...]
        $discriminatorMap = static::getDiscriminatorMap();
        // 2. Flip it: [DvdProduct::class => 'dvd', ...]
        $flippedMap = array_flip($discriminatorMap);
        // 3. Get the current object's class name and find its type string.
        $data['type'] = $flippedMap[get_class($this)] ?? null;
        // --- END OF NEW PART ---

        return $data;
    }
}