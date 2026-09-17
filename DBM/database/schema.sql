-- ============================================================
-- Provision Shop Ordering System - Database Schema
-- (with product images already linked)
-- ============================================================
 
DROP DATABASE IF EXISTS provision_shop;
CREATE DATABASE provision_shop;
USE provision_shop;
 
CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100) NOT NULL,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('staff', 'admin') NOT NULL DEFAULT 'staff',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
 
CREATE TABLE products (
    product_id    INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    category      VARCHAR(80)  NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    stock_qty     INT NOT NULL DEFAULT 0,
    image_url     VARCHAR(255) DEFAULT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
 
CREATE TABLE orders (
    order_id        INT AUTO_INCREMENT PRIMARY KEY,
    reference_no    VARCHAR(20) NOT NULL UNIQUE,
    customer_name   VARCHAR(100) NOT NULL,
    customer_contact VARCHAR(50) NOT NULL,
    order_date      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status          ENUM('Pending', 'Prepared', 'Collected') NOT NULL DEFAULT 'Pending',
    total_amount    DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;
 
CREATE TABLE order_items (
    orderitem_id  INT AUTO_INCREMENT PRIMARY KEY,
    order_id      INT NOT NULL,
    product_id    INT NOT NULL,
    quantity      INT NOT NULL,
    unit_price    DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(order_id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT
) ENGINE=InnoDB;
 
-- Default admin login: username "admin", password "password"
INSERT INTO users (full_name, username, password_hash, role)
VALUES ('System Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
 
INSERT INTO products (name, category, price, stock_qty, image_url) VALUES
('Rice (5kg)',        'Grains',      12.50, 40, 'images/rice.png'),
('Cooking Oil (1L)',   'Cooking',      4.75, 60, 'images/cooking_oil.png'),
('Sugar (1kg)',        'Baking',       1.80, 100, 'images/sugar.png'),
('Milk Powder (400g)', 'Dairy',        6.20, 35, 'images/milk_powder.png'),
('Bread Loaf',         'Bakery',       1.50, 25, 'images/bread_loaf.png'),
('Canned Tomatoes',    'Canned Goods', 0.95, 80, 'images/canned_tomatoes.png');
 

