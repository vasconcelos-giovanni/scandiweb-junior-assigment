# Scandiweb Junior Developer Test Assignment - Backend API

This document provides comprehensive documentation for the PHP backend API of the Scandiweb Junior Developer Test Assignment. The system is a **Product Management REST API** built with pure PHP (no frameworks), following OOP principles, PSR standards, and clean architecture patterns.

---

## Table of Contents

- [Overview](#overview)
- [Key Requirements Met](#key-requirements-met)
- [Architecture](#architecture)
- [Folder Structure](#folder-structure)
- [Core Framework](#core-framework)
  - [Application Lifecycle](#application-lifecycle)
  - [Dependency Injection Container](#dependency-injection-container)
  - [Routing System](#routing-system)
  - [Middleware Pipeline](#middleware-pipeline)
  - [Request and Response](#request-and-response)
  - [Database Layer](#database-layer)
  - [Query Builder](#query-builder)
  - [Entity Base Class](#entity-base-class)
  - [Repository Pattern](#repository-pattern)
- [Domain Layer](#domain-layer)
  - [Product Interface](#product-interface)
  - [Product Entities](#product-entities)
  - [Polymorphism Implementation](#polymorphism-implementation)
- [Application Layer](#application-layer)
  - [Data Transfer Objects (DTOs)](#data-transfer-objects-dtos)
  - [Product Service](#product-service)
- [Infrastructure Layer](#infrastructure-layer)
  - [Factories](#factories)
  - [Hydrators](#hydrators)
  - [Product Repository](#product-repository)
- [HTTP Layer](#http-layer)
  - [Controllers](#controllers)
  - [Middlewares](#middlewares)
- [Service Providers](#service-providers)
- [Exception Handling](#exception-handling)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Design Patterns Used](#design-patterns-used)
- [Configuration](#configuration)
- [Getting Started](#getting-started)

---

## Overview

This project implements a **RESTful API** for managing products with three distinct types:
- **DVD** - with `size` attribute (in MB)
- **Book** - with `weight` attribute (in Kg)
- **Furniture** - with `height`, `width`, and `length` attributes (dimensions)

The architecture follows **Domain-Driven Design (DDD)** principles with **Class Table Inheritance (CTI)** for storing product data.

---

## Key Requirements Met

| Requirement | Implementation |
|-------------|----------------|
| PHP ^7.0 | Uses PHP 7.0+ features with strict types everywhere |
| No frameworks | Custom-built micro-framework inspired by Symfony/Laravel |
| OOP approach | Abstract classes, interfaces, polymorphism |
| PSR standards | PSR-4 autoloading, PSR-12 coding standards |
| No conditionals for type differences | Strategy pattern, Factory pattern, DTO pattern |
| Single endpoint for all product types | POST `/products` handles all types |
| MySQL ^5.6 | PDO-based database layer with CTI pattern |

---

## Architecture

The application follows a **Clean Architecture / Layered Architecture** approach with clear separation of concerns:

```
┌────────────────────────────────────────────────────────────────┐
│                   Entry Point (public/index.php)               │
├────────────────────────────────────────────────────────────────┤
│                Core Layer (App, Router, Container, DB)         │
├────────────────────────────────────────────────────────────────┤
│              Middleware Pipeline (Exception, Response)         │
├────────────────────────────────────────────────────────────────┤
│                HTTP Layer (Controllers, Requests)              │
├────────────────────────────────────────────────────────────────┤
│            Application Layer (Services, DTOs, Resolvers)       │
├────────────────────────────────────────────────────────────────┤
│              Domain Layer (Entities, Contracts/Interfaces)     │
├────────────────────────────────────────────────────────────────┤
│      Infrastructure Layer (Factories, Hydrators, Repositories) │
├────────────────────────────────────────────────────────────────┤
│                  Database Layer (PDO, QueryBuilder)            │
└────────────────────────────────────────────────────────────────┘
```

---

## Folder Structure

```
api/
├── .env                            # Environment configuration
├── .env.example                    # Example environment file
├── composer.json                   # Composer dependencies and autoloading
├── phpcs.xml                       # PHP CodeSniffer configuration
├── migrate.php                     # Database migration runner
│
├── public/
│   └── index.php                   # Application entry point
│
├── routes/
│   └── api.php                     # API route definitions
│
├── database/
│   └── scripts/
│       └── db_scandiweb_junior_assigment.sql  # Database schema (CTI)
│
├── app/
│   ├── Core/                       # Framework Core Components
│   │   ├── App.php                 # Application bootstrap and lifecycle
│   │   ├── AnnotationParser.php    # PHPDoc annotation parser
│   │   ├── Config.php              # Environment configuration loader
│   │   ├── Container.php           # Dependency Injection Container
│   │   ├── Database.php            # PDO database connection
│   │   ├── DB.php                  # Database facade for static access
│   │   ├── Entity.php              # Base entity class with CTI support
│   │   ├── HttpStatus.php          # HTTP status code constants
│   │   ├── MiddlewareInterface.php # Middleware contract
│   │   ├── MiddlewarePipeline.php  # Middleware execution pipeline
│   │   ├── Migration.php           # Migration runner
│   │   ├── MigrationGenerator.php  # Generates SQL from entity annotations
│   │   ├── QueryBuilder.php        # Fluent SQL query builder
│   │   ├── Repository.php          # Abstract base repository
│   │   ├── Request.php             # HTTP request wrapper
│   │   ├── Response.php            # HTTP response builder
│   │   └── Router.php              # HTTP routing engine
│   │
│   ├── Domain/                     # Domain Layer (DDD)
│   │   ├── Contracts/
│   │   │   └── ProductInterface.php    # Product contract
│   │   └── Entities/
│   │       ├── Product.php             # Abstract base product
│   │       ├── DvdProduct.php          # DVD product implementation
│   │       ├── BookProduct.php         # Book product implementation
│   │       └── FurnitureProduct.php    # Furniture product implementation
│   │
│   ├── Application/                # Application Layer
│   │   ├── Dto/                    # Data Transfer Objects
│   │   │   ├── CreateProductDtoInterface.php
│   │   │   ├── CreateProductDto.php    # Abstract base DTO
│   │   │   ├── CreateDvdDto.php
│   │   │   ├── CreateBookDto.php
│   │   │   ├── CreateFurnitureDto.php
│   │   │   └── DtoResolver.php         # DTO type resolver
│   │   └── Services/
│   │       └── ProductService.php      # Business logic layer
│   │
│   ├── Infrastructure/             # Infrastructure Layer
│   │   ├── Factories/              # Product creation factories
│   │   │   ├── ProductFactory.php          # Abstract factory
│   │   │   ├── ProductFactoryResolver.php  # Factory resolver
│   │   │   ├── DvdProductFactory.php
│   │   │   ├── BookProductFactory.php
│   │   │   └── FurnitureProductFactory.php
│   │   ├── Hydrators/              # Database-to-object mappers
│   │   │   ├── ProductHydratorInterface.php
│   │   │   ├── ProductHydratorRegistry.php
│   │   │   ├── DvdProductHydrator.php
│   │   │   ├── BookProductHydrator.php
│   │   │   └── FurnitureProductHydrator.php
│   │   └── Repositories/
│   │       └── ProductRepository.php   # Data access with CTI support
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ProductController.php   # Product HTTP handlers
│   │   └── Middlewares/
│   │       ├── ExceptionHandlerMiddleware.php
│   │       ├── ResponseEmitterMiddleware.php
│   │       └── SystemRoutesMiddleware.php
│   │
│   ├── Exceptions/                 # Custom exceptions
│   │   ├── DuplicateSkuException.php
│   │   ├── NotFoundException.php
│   │   └── ValidationException.php
│   │
│   ├── Helpers/
│   │   └── helpers.php             # Global helper functions (dd)
│   │
│   └── Providers/                  # Service providers
│       ├── ServiceProvider.php         # Abstract base provider
│       ├── ProductServiceProvider.php  # Product-related bindings
│       └── RouteServiceProvider.php    # Route loading
│
└── vendor/                         # Composer dependencies
```

---

## Core Framework

### Application Lifecycle

The application lifecycle is managed by `App\Core\App`, which orchestrates the entire request-response cycle.

#### Entry Point (`public/index.php`)

```php
<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Providers\ProductServiceProvider;
use App\Providers\RouteServiceProvider;

// Create the application instance
$app = new App();

// Register service providers
$app->register(new RouteServiceProvider($app->getContainer()));
$app->register(new ProductServiceProvider($app->getContainer()));

$app->run();
```

#### Application Bootstrap Flow

```
1. App instantiation
   ├── Create Container
   ├── Create Router
   ├── Register core services (Request, Config, Database, Router)
   └── Register global middleware

2. Service provider registration
   ├── RouteServiceProvider::register()
   └── ProductServiceProvider::register()

3. app->run()
   ├── boot() - Boot all service providers
   │   └── RouteServiceProvider::boot() - Load routes
   └── handleRequest()
       ├── Create MiddlewarePipeline
       ├── Execute through global middleware
       ├── Resolve route action using Reflection API
       ├── Auto-inject dependencies into controller methods
       └── Send response
```

### Dependency Injection Container

The `Container` class implements a powerful IoC (Inversion of Control) container supporting:

- **Bindings**: Register concrete implementations
- **Singletons**: Single instance throughout the request
- **Instances**: Pre-built object instances
- **Resolving callbacks**: Post-resolution hooks

```php
class Container
{
    // Register a simple binding
    public function bind(string $abstract, $concrete): void;

    // Register a singleton (single instance)
    public function singleton(string $abstract, $concrete): void;

    // Resolve a service from the container
    public function make(string $abstract);

    // Store an already-resolved instance
    public function instance(string $abstract, $instance): void;

    // Check if a binding exists
    public function has(string $abstract): bool;

    // Register a callback to run after resolution
    public function resolving(string $abstract, \Closure $callback): void;
}
```

#### Usage Examples

```php
// Simple binding
$container->bind(SomeInterface::class, SomeImplementation::class);

// Singleton with factory
$container->singleton(Database::class, function ($container) {
    return new Database($container->make(Config::class));
});

// Pre-built instance
$container->instance('app', $this);

// Resolving callback
$container->resolving(Database::class, function ($database) {
    DB::setDatabase($database);
});
```

### Routing System

The `Router` class handles HTTP routing with support for GET, POST, and DELETE methods.

```php
class Router
{
    public function get(string $route, $action): self;
    public function post(string $route, $action): self;
    public function delete(string $route, $action): self;
    public function resolve(string $requestMethod, string $requestUri);
}
```

#### Route Definition (`routes/api.php`)

```php
<?php
declare(strict_types=1);

use App\Core\Response;
use App\Http\Controllers\ProductController;

// Health check route
$router->get('/test', function () {
    return Response::json([
        'message' => 'API is working!',
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
});

// List all products
$router->get('/products', [ProductController::class, 'index']);

// Create a new product
$router->post('/products', [ProductController::class, 'store']);

// Mass delete products by IDs
$router->delete('/products', [ProductController::class, 'destroy']);
```

### Middleware Pipeline

The middleware system follows the "onion" pattern, where each middleware wraps the next.

#### MiddlewareInterface

```php
interface MiddlewareInterface
{
    public function handle(\Closure $next);
}
```

#### Global Middleware Stack

| Order | Middleware | Purpose |
|-------|-----------|---------|
| 1 | `SystemRoutesMiddleware` | Handles system routes (favicon, robots.txt) |
| 2 | `ExceptionHandlerMiddleware` | Catches and formats exceptions as JSON |
| 3 | `ResponseEmitterMiddleware` | Sends HTTP response to client |

### Request and Response

#### Request Class

Wraps PHP superglobals into an object-oriented interface:

```php
class Request
{
    public function getMethod(): string;
    public function getUri(): string;
    public function getHeader(string $key): ?string;
    public function getHeaders(): array;
    public function getQuery(string $key, $default = null);
    public function getQueryParams(): array;
    public function getPost(string $key, $default = null);
    public function getPostParams(): array;
    public function getJson(): ?array;
}
```

#### Response Class

Fluent builder for HTTP responses:

```php
class Response
{
    public static function json(mixed $data, int $status = 200): self;
    public static function success(mixed $data, string $message = 'Success'): self;
    public static function error(string $message, mixed $data = null, int $status = 400): self;
    public static function validationError(array $errors): self;
    public static function notFound(string $message = 'Resource not found'): self;
    public static function created(mixed $data = null, string $message = '...'): self;
}
```

### Database Layer

#### Database Class

PDO wrapper with multi-driver support:

```php
class Database
{
    public function getConnection(): PDO;
    public function query(string $sql, array $params = []);
    public function lastInsertId(): string;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollBack(): bool;
}
```

#### DB Facade

Static facade for convenient database access:

```php
class DB
{
    public static function setDatabase(Database $database): void;
    public static function getConnection(): PDO;
    public static function table(string $table): QueryBuilder;
    public static function raw(string $sql, array $params = []);
    public static function beginTransaction(): bool;
    public static function commit(): bool;
    public static function rollBack(): bool;
}
```

### Query Builder

Fluent SQL query builder with comprehensive features:

```php
class QueryBuilder
{
    // Selection
    public function table(string $table): self;
    public function select(array $columns = ['*']): self;
    
    // Conditions
    public function where(string $column, string $operator, $value): self;
    public function whereIn(string $column, array $values): self;
    public function whereNull(string $column): self;
    public function whereNotNull(string $column): self;
    
    // Joins
    public function join(string $table, string $first, string $operator, string $second): self;
    public function leftJoin(string $table, string $first, string $operator, string $second): self;
    public function rightJoin(string $table, string $first, string $operator, string $second): self;
    
    // Ordering & Pagination
    public function orderBy(string $column, string $direction = 'ASC'): self;
    public function limit(int $limit): self;
    public function offset(int $offset): self;
    
    // Execution
    public function get(): array;
    public function first(): ?array;
    public function exists(): bool;
    public function count(): int;
    public function insert(array $data): int;
    public function insertRaw(array $data): int;  // Without auto-transaction
    public function update(array $data): int;
    public function delete(): int;
}
```

#### Usage Examples

```php
// Select all products with joins
$products = DB::table('products')
    ->select(['products.*', 'dvd_products.size'])
    ->leftJoin('dvd_products', 'products.id', '=', 'dvd_products.id')
    ->orderBy('products.id', 'ASC')
    ->get();

// Find by condition
$product = DB::table('products')
    ->where('sku', '=', 'ABC123')
    ->first();

// Insert new record
$id = DB::table('products')->insert([
    'sku' => 'XYZ789',
    'name' => 'Test Product',
    'price' => 29.99,
    'type' => 'dvd'
]);

// Delete multiple by SKUs
DB::table('products')
    ->whereIn('sku', ['ABC', 'DEF'])
    ->delete();
```

### Entity Base Class

The `Entity` base class provides CTI (Class Table Inheritance) support using annotations:

```php
abstract class Entity
{
    public static function getTableName(): string;
    public static function getDiscriminatorColumn(): ?string;
    public static function getDiscriminatorMap(): array;
}
```

#### Annotation Example

```php
/**
 * @Table(name="products")
 * @DiscriminatorColumn(name="type")
 * @DiscriminatorMap(
 *     dvd="App\\Domain\\Entities\\DvdProduct",
 *     book="App\\Domain\\Entities\\BookProduct",
 *     furniture="App\\Domain\\Entities\\FurnitureProduct"
 * )
 */
abstract class Product extends Entity implements ProductInterface
{
    /**
     * @Column(type="INT", options="PRIMARY KEY AUTO_INCREMENT")
     */
    protected int $id;
    
    /**
     * @Column(type="VARCHAR(255)", options="NOT NULL UNIQUE")
     */
    protected string $sku;
    
    // ...
}
```

### Repository Pattern

The abstract `Repository` class provides common CRUD operations:

```php
abstract class Repository
{
    abstract protected function getEntityClass(): string;
    abstract protected function hydrate(array $data): Entity;
    
    protected function getTableName(): string;
    protected function query(): QueryBuilder;
    protected function getDatabase(): Database;
    
    public function find(int $id): ?Entity;
    public function findAll(): array;
    public function findBy(string $field, $value): array;
    public function findOneBy(string $field, $value): ?Entity;
    public function exists(string $field, $value): bool;
    public function count(): int;
    public function delete(int $id): int;
    public function deleteByIds(array $ids): int;
    
    protected function beginTransaction(): bool;
    protected function commit(): bool;
    protected function rollBack(): bool;
}
```

---

## Domain Layer

### Product Interface

Defines the contract all products must implement:

```php
interface ProductInterface
{
    public function getId(): int;
    public function getSku(): string;
    public function getName(): string;
    public function getPrice(): float;
    public function getType(): string;
    
    public function setId(int $id);
    public function setSku(string $sku);
    public function setName(string $name);
    public function setPrice(float $price);
    
    public function getSpecificAttribute(): string;
    public function getSpecificAttributesArray(): array;
    public function toArray(): array;
}
```

### Product Entities

#### Abstract Product Class

```php
/**
 * @Table(name="products")
 * @DiscriminatorColumn(name="type")
 * @DiscriminatorMap(...)
 */
abstract class Product extends Entity implements ProductInterface
{
    protected int $id = 0;
    protected string $sku = '';
    protected string $name = '';
    protected float $price = 0.0;
    
    // Getters and fluent setters...
    
    abstract public function getType(): string;
    abstract public function getSpecificAttribute(): string;
    abstract public function getSpecificAttributesArray(): array;
    
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'sku' => $this->getSku(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'type' => $this->getType(),
            'specific_attribute' => $this->getSpecificAttribute(),
        ];
    }
}
```

### Polymorphism Implementation

Each product type implements its specific behavior **without conditionals**:

#### DvdProduct

```php
/**
 * @Table(name="dvd_products")
 */
class DvdProduct extends Product
{
    protected int $size = 0;
    
    public function getType(): string
    {
        return 'dvd';
    }
    
    public function getSpecificAttribute(): string
    {
        return "Size: {$this->size} MB";
    }
    
    public function getSpecificAttributesArray(): array
    {
        return ['size' => $this->size];
    }
}
```

#### BookProduct

```php
/**
 * @Table(name="book_products")
 */
class BookProduct extends Product
{
    protected float $weight = 0.0;
    
    public function getType(): string
    {
        return 'book';
    }
    
    public function getSpecificAttribute(): string
    {
        return "Weight: {$this->weight} KG";
    }
    
    public function getSpecificAttributesArray(): array
    {
        return ['weight' => $this->weight];
    }
}
```

#### FurnitureProduct

```php
/**
 * @Table(name="furniture_products")
 */
class FurnitureProduct extends Product
{
    protected int $height = 0;
    protected int $width = 0;
    protected int $length = 0;
    
    public function getType(): string
    {
        return 'furniture';
    }
    
    public function getSpecificAttribute(): string
    {
        return "Dimension: {$this->height}x{$this->width}x{$this->length}";
    }
    
    public function getSpecificAttributesArray(): array
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
        ];
    }
}
```

---

## Application Layer

### Data Transfer Objects (DTOs)

DTOs handle validation and data transfer **without conditionals** using polymorphism:

#### CreateProductDtoInterface

```php
interface CreateProductDtoInterface
{
    public static function getType(): string;
    public static function fromArray(array $data);
    public function getSku(): string;
    public function getName(): string;
    public function getPrice(): float;
    public function toArray(): array;
}
```

#### Abstract CreateProductDto

```php
abstract class CreateProductDto implements CreateProductDtoInterface
{
    protected string $sku;
    protected string $name;
    protected float $price;
    
    abstract public static function getType(): string;
    abstract protected function validateSpecificFields(array $data): array;
    abstract protected function hydrateSpecificFields(array $data): void;
    abstract protected function getSpecificFieldsArray(): array;
    
    public static function fromArray(array $data): CreateProductDtoInterface
    {
        $dto = new static();
        $dto->validate($data);  // Throws ValidationException if invalid
        $dto->hydrate($data);
        return $dto;
    }
    
    protected function validate(array $data): void
    {
        $errors = [];
        $errors = array_merge($errors, $this->validateCommonFields($data));
        $errors = array_merge($errors, $this->validateSpecificFields($data));
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
```

#### Type-Specific DTOs

```php
class CreateDvdDto extends CreateProductDto
{
    protected int $size;
    
    public static function getType(): string { return 'dvd'; }
    
    protected function validateSpecificFields(array $data): array
    {
        $errors = [];
        if (!isset($data['size']) || !is_numeric($data['size'])) {
            $errors['size'] = 'Please, submit required data';
        }
        return $errors;
    }
    
    protected function hydrateSpecificFields(array $data): void
    {
        $this->size = (int)$data['size'];
    }
}
```

#### DtoResolver

Resolves the correct DTO class based on product type:

```php
class DtoResolver
{
    public function resolve(array $data): CreateProductDtoInterface
    {
        $type = strtolower($data['type']);
        $key = "product.dto.{$type}";
        
        if (!$this->container->has($key)) {
            throw new ValidationException(['type' => 'Invalid product type']);
        }
        
        $dtoClass = $this->container->make($key);
        return $dtoClass::fromArray($data);
    }
}
```

### Product Service

Business logic layer that orchestrates DTOs, Factories, and Repository:

```php
class ProductService
{
    public function __construct(
        ProductRepository $productRepository,
        ProductFactoryResolver $factoryResolver,
        DtoResolver $dtoResolver
    );
    
    public function getAllProducts(): array;
    public function getProductById(int $id): ?array;
    public function getProductBySku(string $sku): ?array;
    
    public function createProduct(array $data): ProductInterface
    {
        // 1. Validate and create DTO (polymorphic validation)
        $dto = $this->dtoResolver->resolve($data);
        
        // 2. Get factory and create entity (polymorphic creation)
        $factory = $this->factoryResolver->resolve($dto::getType());
        $product = $factory->createFromDto($dto);
        
        // 3. Persist the product
        return $this->productRepository->save($product);
    }
    
    public function createProductFromDto(CreateProductDtoInterface $dto): ProductInterface;
    public function deleteProductsBySkus(array $skus): int;
    public function deleteProductsByIds(array $ids): int;
}
```

---

## Infrastructure Layer

### Factories

The Factory pattern creates product entities from DTOs **without conditionals**:

#### Abstract ProductFactory

```php
abstract class ProductFactory
{
    abstract public function createFromDto(CreateProductDtoInterface $dto): ProductInterface;
    abstract public function createFromArray(array $data): ProductInterface;
}
```

#### Type-Specific Factories

```php
class DvdProductFactory extends ProductFactory
{
    public function createFromDto(CreateProductDtoInterface $dto): ProductInterface
    {
        $product = new DvdProduct();
        return $product
            ->setSku($dto->getSku())
            ->setName($dto->getName())
            ->setPrice($dto->getPrice())
            ->setSize($dto->getSize());
    }
}
```

#### ProductFactoryResolver

```php
class ProductFactoryResolver
{
    public function __construct(Container $container);
    
    public function resolve(string $type): ProductFactory
    {
        $key = "product.factory.{$type}";
        if (!$this->container->has($key)) {
            throw new \InvalidArgumentException("No factory for type: {$type}");
        }
        return $this->container->make($key);
    }
    
    public function hasFactory(string $type): bool;
}
```

### Hydrators

Hydrators transform database rows into domain entities **without conditionals**:

```php
interface ProductHydratorInterface
{
    public function hydrate(array $data): ProductInterface;
}

class DvdProductHydrator implements ProductHydratorInterface
{
    public function hydrate(array $data): ProductInterface
    {
        $product = new DvdProduct();
        return $product
            ->setId((int)$data['id'])
            ->setSku((string)$data['sku'])
            ->setName((string)$data['name'])
            ->setPrice((float)$data['price'])
            ->setSize((int)$data['size']);
    }
}
```

#### ProductHydratorRegistry

```php
class ProductHydratorRegistry
{
    public function register(string $type, ProductHydratorInterface $hydrator): void;
    public function get(string $type): ProductHydratorInterface;
    public function has(string $type): bool;
    public function getRegisteredTypes(): array;
}
```

### Product Repository

Handles CTI data access with JOIN queries:

```php
class ProductRepository extends Repository
{
    public function findAll(): array
    {
        $rows = DB::table('products')
            ->select([
                'products.id',
                'products.sku',
                'products.name',
                'products.price',
                'products.type',
                'dvd_products.size',
                'book_products.weight',
                'furniture_products.height',
                'furniture_products.width',
                'furniture_products.length'
            ])
            ->leftJoin('dvd_products', 'products.id', '=', 'dvd_products.id')
            ->leftJoin('book_products', 'products.id', '=', 'book_products.id')
            ->leftJoin('furniture_products', 'products.id', '=', 'furniture_products.id')
            ->orderBy('products.id', 'ASC')
            ->get();
        // ... hydrate results using registry
    }
    
    public function save(ProductInterface $product): ProductInterface
    {
        $this->beginTransaction();
        try {
            // 1. Insert into parent table
            $productId = DB::table('products')->insertRaw([...]);
            
            // 2. Insert into child table
            $childTableName = $this->getChildTableName($product->getType());
            DB::table($childTableName)->insertRaw([
                'id' => $productId,
                ...$product->getSpecificAttributesArray()
            ]);
            
            $this->commit();
            $product->setId($productId);
            return $product;
        } catch (\PDOException $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
```

---

## HTTP Layer

### Controllers

```php
class ProductController
{
    public function __construct(ProductService $productService);
    
    public function index(): Response
    {
        $products = $this->productService->getAllProducts();
        return Response::json($products);
    }
    
    public function store(Request $request): Response
    {
        $data = $request->getJson();
        $product = $this->productService->createProduct($data);
        return Response::created(
            $product->toArray(),
            'Product created successfully.'
        );
    }
    
    public function destroy(Request $request): Response
    {
        $data = $request->getJson();
        $ids = array_map('intval', $data['ids']);
        $this->productService->deleteProductsByIds($ids);
        return Response::json(['message' => 'Products deleted successfully.']);
    }
}
```

### Middlewares

| Middleware | Purpose |
|------------|---------|
| `SystemRoutesMiddleware` | Handles favicon.ico, robots.txt |
| `ExceptionHandlerMiddleware` | Converts exceptions to JSON responses |
| `ResponseEmitterMiddleware` | Sends final HTTP response |

---

## Service Providers

### ProductServiceProvider

Registers all product-related dependencies:

```php
class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerDtos();      // DTO class mappings
        $this->registerFactories(); // Factory instances
        $this->registerHydrators(); // Hydrator registry
        $this->registerRepositories();
        $this->registerServices();
    }
    
    private function registerDtos(): void
    {
        $this->container->bind('product.dto.dvd', CreateDvdDto::class);
        $this->container->bind('product.dto.book', CreateBookDto::class);
        $this->container->bind('product.dto.furniture', CreateFurnitureDto::class);
        // ...
    }
}
```

---

## Exception Handling

| Exception | HTTP Status | Usage |
|-----------|-------------|-------|
| `ValidationException` | 422 | Invalid input data |
| `DuplicateSkuException` | 409 | SKU already exists |
| `NotFoundException` | 404 | Resource not found |

All exceptions are caught by `ExceptionHandlerMiddleware` and returned as JSON:

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "sku": "Please, submit required data",
        "size": "Please, provide the data of indicated type"
    }
}
```

---

## Database Schema

The schema uses **Class Table Inheritance (CTI)**:

```sql
-- Create database (MySQL)
CREATE DATABASE IF NOT EXISTS db_scandiweb_jr_assigment;
USE db_scandiweb_jr_assigment;

-- Parent table (common attributes)
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    type ENUM('dvd', 'book', 'furniture') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_sku (sku),
    INDEX idx_type (type)
);

-- Child tables (type-specific attributes)
CREATE TABLE dvd_products (
    id INT PRIMARY KEY,
    size INT NOT NULL COMMENT 'Size in megabytes (MB)',
    FOREIGN KEY (id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE book_products (
    id INT PRIMARY KEY,
    weight DECIMAL(10, 2) NOT NULL COMMENT 'Weight in kilograms (Kg)',
    FOREIGN KEY (id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE furniture_products (
    id INT PRIMARY KEY,
    height INT NOT NULL COMMENT 'Height dimension',
    width INT NOT NULL COMMENT 'Width dimension',
    length INT NOT NULL COMMENT 'Length dimension',
    FOREIGN KEY (id) REFERENCES products(id) ON DELETE CASCADE
);
```

---

## API Endpoints

### GET /test

Health check endpoint to verify the API is running.

**Response:**
```json
{
    "message": "API is working!",
    "timestamp": "2024-01-15 10:30:00"
}
```

### GET /products

List all products.

**Response:**
```json
[
    {
        "id": 1,
        "sku": "DVD-001",
        "name": "Inception",
        "price": 19.99,
        "type": "dvd",
        "specific_attribute": "Size: 700 MB"
    },
    {
        "id": 2,
        "sku": "BOOK-001",
        "name": "Clean Code",
        "price": 29.99,
        "type": "book",
        "specific_attribute": "Weight: 0.5 KG"
    }
]
```

### POST /products

Create a new product.

**Request (DVD):**
```json
{
    "sku": "DVD-002",
    "name": "The Matrix",
    "price": 14.99,
    "type": "dvd",
    "size": 800
}
```

**Request (Book):**
```json
{
    "sku": "BOOK-002",
    "name": "Design Patterns",
    "price": 49.99,
    "type": "book",
    "weight": 1.2
}
```

**Request (Furniture):**
```json
{
    "sku": "FURN-001",
    "name": "Office Desk",
    "price": 299.99,
    "type": "furniture",
    "height": 75,
    "width": 120,
    "length": 60
}
```

**Response (201):**
```json
{
    "status": "success",
    "message": "Product created successfully.",
    "data": {
        "id": 3,
        "sku": "DVD-002",
        "name": "The Matrix",
        "price": 14.99,
        "type": "dvd",
        "specific_attribute": "Size: 800 MB"
    }
}
```

### DELETE /products

Mass delete products by IDs.

**Request:**
```json
{
    "ids": [1, 2, 3]
}
```

**Response:**
```json
{
    "message": "Products deleted successfully."
}
```

---

## Design Patterns Used

| Pattern | Usage | Benefit |
|---------|-------|---------|
| **Factory Method** | ProductFactory, type-specific factories | Avoids conditionals for object creation |
| **Strategy** | DTOs with polymorphic validation | Avoids conditionals for validation |
| **Repository** | ProductRepository | Abstracts data access layer |
| **Data Mapper** | Hydrators | Transforms DB rows to entities |
| **Dependency Injection** | Container, auto-wiring | Loose coupling, testability |
| **Facade** | DB class | Simple static interface |
| **Service Layer** | ProductService | Separates business logic |
| **DTO** | CreateProductDto classes | Type-safe data transfer |

---

## Configuration

### Environment Variables (`.env`)

```env
# Application Settings
APP_ENV=development
APP_DEBUG=true

# Database Configuration (SQLite - default for development)
DB_DRIVER=sqlite
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=database/database.sqlite
DB_USERNAME=root
DB_PASSWORD=root

# For MySQL production:
# DB_DRIVER=mysql
# DB_HOST=localhost
# DB_PORT=3306
# DB_DATABASE=db_scandiweb_junior_assigment
# DB_USERNAME=root
# DB_PASSWORD=
```

---

## Getting Started

### Requirements

- PHP ^7.0
- MySQL ^5.6 (for production) or SQLite (for development)
- Composer

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd api
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Configure database in `.env`:
   - **SQLite (default)**: No additional configuration needed
   - **MySQL**: Update `DB_DRIVER=mysql` and set connection details

5. For MySQL, run database migrations:
```bash
mysql -u root -p < database/scripts/db_scandiweb_junior_assigment.sql
```

6. Start development server:
```bash
php -S localhost:8000 -t public
```

7. Test the API:
```bash
# Health check
curl http://localhost:8000/test

# List products
curl http://localhost:8000/products
```

---

## License

This project is created as a test assignment for Scandiweb.
