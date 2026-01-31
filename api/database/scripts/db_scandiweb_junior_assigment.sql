-- ============================================================================
-- Scandiweb Junior Assignment Database Schema
-- ============================================================================
-- This schema implements Class Table Inheritance (CTI) for products.
-- The parent 'products' table stores common attributes.
-- Child tables store type-specific attributes and reference the parent via FK.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS db_scandiweb_junior_assigment;
USE db_scandiweb_junior_assigment;

-- ============================================================================
-- Parent Table: products
-- ============================================================================
-- Stores common product attributes shared by all product types.
-- The 'type' column acts as a discriminator for polymorphism.

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    type ENUM('dvd', 'book', 'furniture') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_sku (sku),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- Child Table: dvd_products
-- ============================================================================
-- Stores DVD-specific attributes (size in MB).
-- Uses the same ID as the parent products table (CTI pattern).

CREATE TABLE IF NOT EXISTS dvd_products (
    id INT PRIMARY KEY,
    size INT NOT NULL COMMENT 'Size in megabytes (MB)',
    
    FOREIGN KEY (id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- Child Table: book_products
-- ============================================================================
-- Stores Book-specific attributes (weight in Kg).
-- Uses the same ID as the parent products table (CTI pattern).

CREATE TABLE IF NOT EXISTS book_products (
    id INT PRIMARY KEY,
    weight DECIMAL(10, 2) NOT NULL COMMENT 'Weight in kilograms (Kg)',
    
    FOREIGN KEY (id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- Child Table: furniture_products
-- ============================================================================
-- Stores Furniture-specific attributes (dimensions: height, width, length).
-- Uses the same ID as the parent products table (CTI pattern).

CREATE TABLE IF NOT EXISTS furniture_products (
    id INT PRIMARY KEY,
    height INT NOT NULL COMMENT 'Height dimension',
    width INT NOT NULL COMMENT 'Width dimension',
    length INT NOT NULL COMMENT 'Length dimension',
    
    FOREIGN KEY (id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- Sample Data (Optional - for testing)
-- ============================================================================

-- INSERT INTO products (sku, name, price, type) VALUES
--     ('DVD-001', 'Inception', 19.99, 'dvd'),
--     ('BOOK-001', 'Clean Code', 29.99, 'book'),
--     ('FURN-001', 'Office Chair', 199.99, 'furniture');

-- INSERT INTO dvd_products (id, size) VALUES (1, 700);
-- INSERT INTO book_products (id, weight) VALUES (2, 0.5);
-- INSERT INTO furniture_products (id, height, width, length) VALUES (3, 120, 60, 60);