-- ============================================================
-- SmartDine Hub - Database Migration
-- Compatible with MySQL 5.7+ / MariaDB 10.x (WAMP/XAMPP)
-- Run this once against your smartdine database
-- ============================================================
use smartdine;

-- 1. Ratings & Reviews
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    restaurant VARCHAR(100) NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 2. Restaurant info
CREATE TABLE IF NOT EXISTS restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    cuisine_type VARCHAR(100),
    region VARCHAR(50),
    address TEXT,
    phone VARCHAR(30),
    email VARCHAR(100),
    opening_hours VARCHAR(255) DEFAULT '8:00 AM - 10:00 PM',
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Delivery zones
CREATE TABLE IF NOT EXISTS delivery_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone_name VARCHAR(100) NOT NULL,
    region VARCHAR(50) NOT NULL,
    fee DECIMAL(10,2) NOT NULL DEFAULT 5000.00
);

-- 4. User activity tracking
CREATE TABLE IF NOT EXISTS user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    page VARCHAR(191) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_page (user_id, page),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    logged_in_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- ALTER TABLE columns (safe approach for MySQL 5.7)
-- Each block checks if the column exists before adding it.
-- ============================================================

-- Add in_stock to products (if missing)
SET @col = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'products'
    AND COLUMN_NAME = 'in_stock'
);
SET @sql = IF(@col = 0,
    'ALTER TABLE products ADD COLUMN in_stock TINYINT(1) NOT NULL DEFAULT 1',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add description to products (if missing)
SET @col = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'products'
    AND COLUMN_NAME = 'description'
);
SET @sql = IF(@col = 0,
    'ALTER TABLE products ADD COLUMN description TEXT',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add phone to users (if missing)
SET @col = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'phone'
);
SET @sql = IF(@col = 0,
    'ALTER TABLE users ADD COLUMN phone VARCHAR(30) DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================
-- Seed data
-- ============================================================

INSERT IGNORE INTO restaurants (name, description, cuisine_type, region, address, phone, opening_hours, image) VALUES
('Smart Dine',      'Classic comfort food and international favourites',  'International', 'Central',  'Kampala, Uganda',     '0700111001', '8:00 AM - 10:00 PM',  'Cheeseburger.jpg'),
('Italian Corner',  'Authentic Italian cuisine with a modern twist',       'Italian',       'Central',  'Kampala, Uganda',     '0700111002', '9:00 AM - 9:00 PM',   'Antipasto Platter.jpg'),
('Dessert Heaven',  'Sweet treats and decadent desserts',                  'Desserts',      'Central',  'Kampala, Uganda',     '0700111003', '10:00 AM - 10:00 PM', 'Cheesecake.jpg'),
('Asian Fusion',    'Bold Asian flavours from across the continent',       'Asian',         'Eastern',  'Jinja, Uganda',       '0700111004', '9:00 AM - 9:00 PM',   'Dumplings.jpg'),
('Fast Food Hub',   'Quick bites and satisfying meals',                    'Fast Food',     'Eastern',  'Mbale, Uganda',       '0700111005', '7:00 AM - 11:00 PM',  'Big Mac.jpg'),
('Seafood Delight', 'Fresh seafood prepared to perfection',                'Seafood',       'Western',  'Mbarara, Uganda',     '0700111006', '10:00 AM - 9:00 PM',  'Grilled Lobster.jpg'),
('Vegan Paradise',  'Plant-based delights for conscious eaters',           'Vegan',         'Western',  'Fort Portal, Uganda', '0700111007', '8:00 AM - 8:00 PM',   'Almond Milk Latte.jpg'),
('Mexican Grill',   'Spicy and flavourful Mexican specialties',            'Mexican',       'Northern', 'Gulu, Uganda',        '0700111008', '9:00 AM - 10:00 PM',  'Burrito Bowl.jpg');

INSERT IGNORE INTO delivery_zones (zone_name, region, fee) VALUES
('Kampala Central', 'Central',  3000),
('Kampala Suburbs', 'Central',  5000),
('Jinja',           'Eastern',  7000),
('Mbale',           'Eastern',  8000),
('Mbarara',         'Western',  9000),
('Fort Portal',     'Western',  10000),
('Gulu',            'Northern', 12000);

-- Add notes and payment_method columns to orders (if missing)
SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'notes');
SET @sql = IF(@col = 0, 'ALTER TABLE orders ADD COLUMN notes TEXT DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_method');
SET @sql = IF(@col = 0, 'ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT ''pay_on_delivery''', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
