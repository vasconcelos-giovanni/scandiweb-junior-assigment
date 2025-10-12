CREATE DATABASE IF NOT EXISTS db_scandiweb_junior_assigment;
USE db_scandiweb_junior_assigment;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    type ENUM('dvd', 'book', 'furniture') NOT NULL
);

CREATE TABLE products_dvd (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    size INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE products_book (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    weight DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE products_furniture (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    height INT NOT NULL,
    width INT NOT NULL,
    length INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);