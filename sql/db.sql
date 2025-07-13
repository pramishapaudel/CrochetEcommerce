-- Yarn-Joy Crochet E-commerce Database Schema
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS yarn_joy;
USE yarn_joy;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    userName VARCHAR(100) NOT NULL,
    Contact VARCHAR(15) NOT NULL UNIQUE,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Address TEXT NOT NULL,
    Password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE IF NOT EXISTS admin (
    adminId INT AUTO_INCREMENT PRIMARY KEY,
    adminName VARCHAR(100) NOT NULL,
    adminNumber VARCHAR(15) NOT NULL UNIQUE,
    adminPassword VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product table
CREATE TABLE IF NOT EXISTS product (
    productId INT AUTO_INCREMENT PRIMARY KEY,
    productName VARCHAR(255) NOT NULL,
    productDetails TEXT,
    productPrice DECIMAL(10,2) NOT NULL,
    productQuantity INT NOT NULL DEFAULT 0,
    productImage VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('active', 'completed', 'abandoned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(userId) ON DELETE CASCADE
);

-- Cart items table
CREATE TABLE IF NOT EXISTS cart_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES cart(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(productId) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    orderId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    productId INT NOT NULL,
    orderQuantity INT NOT NULL,
    status ENUM('pending', 'complete', 'cancelled', 'paid') DEFAULT 'pending',
    payment_method ENUM('cash', 'khalti', 'cod') DEFAULT 'cash',
    transaction_id VARCHAR(255),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES product(productId) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO admin (adminName, adminNumber, adminPassword) VALUES 
('admin', '1234567890', 'admin123')
ON DUPLICATE KEY UPDATE adminName = adminName;

-- Sample products (optional)
INSERT INTO product (productName, productDetails, productPrice, productQuantity, productImage) VALUES 
('Crochet Granny Square Bag', 'Beautiful handmade crochet bag with granny square pattern', 25.00, 10, 'Crochet granny square bag.jpg'),
('Crochet Sunflower Tote', 'Colorful sunflower pattern tote bag', 30.00, 8, 'crochet sunflower tote bag.jpg'),
('Crochet Heart Granny Square', 'Adorable heart pattern granny square', 15.00, 15, 'Crochet Heart Granny Square_ Pattern And Ideas.jpg')
ON DUPLICATE KEY UPDATE productName = productName;
