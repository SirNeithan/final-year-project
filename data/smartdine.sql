CREATE DATABASE smartdine;
USE smartdine;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    restaurant VARCHAR(100) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    restaurant VARCHAR(100) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    delivery_address TEXT NOT NULL,
    delivery_city VARCHAR(100) NOT NULL,
    delivery_zip VARCHAR(20) NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NULL,
    payment_method VARCHAR(50) DEFAULT 'credit_card',
    transaction_id VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price VARCHAR(50) NOT NULL,
    quantity INT DEFAULT 1,
    restaurant VARCHAR(100) NOT NU

-- Demo Users (passwords are hashed using password_hash)
-- User credentials: username/password
-- demo1/password, demo2/password, demo3/password, demo4/password, admin/admin123
INSERT INTO users (username, password, email, role) VALUES
('demo1', '$2y$10$kLzpG6qT5FxWvLqXk6sHr.8q7GqzQqZqZqZqZqZqZqZqZqZqZqZqZ', 'demo1@smartdine.com', 'user'),
('demo2', '$2y$10$kLzpG6qT5FxWvLqXk6sHr.8q7GqzQqZqZqZqZqZqZqZqZqZqZqZqZ', 'demo2@smartdine.com', 'user'),
('demo3', '$2y$10$kLzpG6qT5FxWvLqXk6sHr.8q7GqzQqZqZqZqZqZqZqZqZqZqZqZqZ', 'demo3@smartdine.com', 'user'),
('demo4', '$2y$10$kLzpG6qT5FxWvLqXk6sHr.8q7GqzQqZqZqZqZqZqZqZqZqZqZqZqZ', 'demo4@smartdine.com', 'user'),
('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMye4DQNa7HqFCPsqfXM1UqCfbNZ0q5P8rK', 'admin@smartdine.com', 'admin');

INSERT INTO products (name, price, image, category, restaurant) VALUES
('Bruschetta', 'UGX 25,000', 'Bruschetta.jpg', 'appetizer', 'Smart Dine'),
('Garlic Bread', 'UGX 20,000', 'Garlic-Bread.jpg', 'appetizer', 'Smart Dine'),
('Caesar Salad', 'UGX 30,000', 'Caesar-Salad.jpg', 'appetizer', 'Smart Dine'),
('Chicken Wings', 'UGX 35,000', 'chicken-wings.jpg', 'appetizer', 'Smart Dine'),
('Cheeseburger', 'UGX 45,000', 'Cheeseburger.jpg', 'main-course', 'Smart Dine'),
('Grilled Chicken Burger', 'UGX 50,000', 'Grilled Chicken Burger.jpg', 'main-course', 'Smart Dine'),
('Beef Steak', 'UGX 75,000', 'Beef Steak.jpg', 'main-course', 'Smart Dine'),
('Chicken Parmesan', 'UGX 65,000', 'Chicken-Parmesan.jpg', 'main-course', 'Smart Dine'),
('Chocolate Cake', 'UGX 28,000', 'Chocolate Cake.jpg', 'dessert', 'Smart Dine'),
('Cheesecake', 'UGX 32,000', 'Cheesecake.jpg', 'dessert', 'Smart Dine'),
('Fruit Salad', 'UGX 22,000', 'Fruit Salad.jpg', 'dessert', 'Smart Dine'),
('Coca Cola', 'UGX 20,000', 'Coca Cola.jpg', 'beverage', 'Smart Dine'),
('Coffee', 'UGX 25,000', 'Coffee.jpg', 'beverage', 'Smart Dine'),
('Caprese Salad', 'UGX 30,000', 'Caprese Salad.jpg', 'appetizer', 'Italian Corner'),
('Antipasto Platter', 'UGX 45,000', 'Antipasto Platter.jpg', 'appetizer', 'Italian Corner'),
('Espresso', 'UGX 22,000', 'Espresso.jpg', 'beverage', 'Italian Corner'),
('Dumplings', 'UGX 35,000', 'Dumplings.jpg', 'appetizer', 'Asian Fusion'),
('Green Tea Ice Cream', 'UGX 28,000', 'Green Tea Ice Cream.jpg', 'dessert', 'Asian Fusion'),
('Bubble Tea', 'UGX 25,000', 'Bubble Tea.jpg', 'beverage', 'Asian Fusion'),
('Burrito Bowl', 'UGX 55,000', 'Burrito Bowl.jpg', 'main-course', 'Mexican Grill'),
('Churros', 'UGX 26,000', 'Churros.jpg', 'dessert', 'Mexican Grill'),
('Calamari', 'UGX 42,000', 'Calamari.jpg', 'appetizer', 'Seafood Delight'),
('Grilled Lobster', 'UGX 80,000', 'Grilled Lobster.jpg', 'main-course', 'Seafood Delight'),
('Fish Tacos', 'UGX 48,000', 'Fish Tacos.jpg', 'main-course', 'Seafood Delight'),
('French Fries', 'UGX 20,000', 'French Fries.jpg', 'appetizer', 'Fast Food Hub'),
('Chicken Nuggets', 'UGX 32,000', 'Chicken Nuggets.jpg', 'appetizer', 'Fast Food Hub'),
('Big Mac', 'UGX 50,000', 'Big Mac.jpg', 'main-course', 'Fast Food Hub'),
('Chicken Sandwich', 'UGX 45,000', 'Chicken Sandwich.jpg', 'main-course', 'Fast Food Hub'),
('Gelato', 'UGX 24,000', 'Gelato.jpg', 'dessert', 'Dessert Heaven'),
('Almond Milk Latte', 'UGX 28,000', 'Almond Milk Latte.jpg', 'beverage', 'Vegan Paradise');
