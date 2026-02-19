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
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

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
('Bruschetta', 'UGX 200', 'bruschetta.jpg', 'appetizer', 'Smart Dine'),
('Garlic Bread', 'UGX 150', 'garlic-bread.jpg', 'appetizer', 'Smart Dine'),
('Caesar Salad', 'UGX 250', 'caesar-salad.jpg', 'appetizer', 'Smart Dine'),
('Mozzarella Sticks', 'UGX 180', 'mozzarella-sticks.jpg', 'appetizer', 'Smart Dine'),
('Chicken Wings', 'UGX 300', 'chicken-wings.jpg', 'appetizer', 'Smart Dine'),
('Margherita Pizza', 'UGX 450', 'margherita-pizza.jpg', 'main-course', 'Smart Dine'),
('Pepperoni Pizza', 'UGX 500', 'pepperoni-pizza.jpg', 'main-course', 'Smart Dine'),
('Cheeseburger', 'UGX 350', 'cheeseburger.jpg', 'main-course', 'Smart Dine'),
('Grilled Chicken Burger', 'UGX 400', 'chicken-burger.jpg', 'main-course', 'Smart Dine'),
('Pasta Carbonara', 'UGX 550', 'pasta-carbonara.jpg', 'main-course', 'Smart Dine'),
('Spaghetti Bolognese', 'UGX 480', 'spaghetti-bolognese.jpg', 'main-course', 'Smart Dine'),
('Grilled Salmon', 'UGX 650', 'grilled-salmon.jpg', 'main-course', 'Smart Dine'),
('Chicken Parmesan', 'UGX 600', 'chicken-parmesan.jpg', 'main-course', 'Smart Dine'),
('Beef Steak', 'UGX 700', 'beef-steak.jpg', 'main-course', 'Smart Dine'),
('Vegetable Stir Fry', 'UGX 350', 'veg-stir-fry.jpg', 'main-course', 'Smart Dine'),
('Chocolate Cake', 'UGX 200', 'chocolate-cake.jpg', 'dessert', 'Smart Dine'),
('Tiramisu', 'UGX 250', 'tiramisu.jpg', 'dessert', 'Smart Dine'),
('Ice Cream Sundae', 'UGX 180', 'ice-cream.jpg', 'dessert', 'Smart Dine'),
('Cheesecake', 'UGX 220', 'cheesecake.jpg', 'dessert', 'Smart Dine'),
('Fruit Salad', 'UGX 150', 'fruit-salad.jpg', 'dessert', 'Smart Dine'),
('Coca Cola', 'UGX 80', 'coca-cola.jpg', 'beverage', 'Smart Dine'),
('Orange Juice', 'UGX 100', 'orange-juice.jpg', 'beverage', 'Smart Dine'),
('Coffee', 'UGX 90', 'coffee.jpg', 'beverage', 'Smart Dine'),
('Iced Tea', 'UGX 85', 'iced-tea.jpg', 'beverage', 'Smart Dine'),
('Mineral Water', 'UGX 60', 'water.jpg', 'beverage', 'Smart Dine'),
('Caprese Salad', 'UGX 220', 'caprese.jpg', 'appetizer', 'Italian Corner'),
('Antipasto Platter', 'UGX 350', 'antipasto.jpg', 'appetizer', 'Italian Corner'),
('Lasagna', 'UGX 520', 'lasagna.jpg', 'main-course', 'Italian Corner'),
('Risotto', 'UGX 480', 'risotto.jpg', 'main-course', 'Italian Corner'),
('Tiramisu', 'UGX 250', 'tiramisu.jpg', 'dessert', 'Italian Corner'),
('Espresso', 'UGX 95', 'espresso.jpg', 'beverage', 'Italian Corner'),
('Spring Rolls', 'UGX 180', 'spring-rolls.jpg', 'appetizer', 'Asian Fusion'),
('Dumplings', 'UGX 200', 'dumplings.jpg', 'appetizer', 'Asian Fusion'),
('Kung Pao Chicken', 'UGX 450', 'kung-pao.jpg', 'main-course', 'Asian Fusion'),
('Pad Thai', 'UGX 420', 'pad-thai.jpg', 'main-course', 'Asian Fusion'),
('Green Tea Ice Cream', 'UGX 190', 'green-tea-ice.jpg', 'dessert', 'Asian Fusion'),
('Bubble Tea', 'UGX 120', 'bubble-tea.jpg', 'beverage', 'Asian Fusion'),
('Nachos', 'UGX 280', 'nachos.jpg', 'appetizer', 'Mexican Grill'),
('Quesadillas', 'UGX 320', 'quesadillas.jpg', 'appetizer', 'Mexican Grill'),
('Tacos', 'UGX 350', 'tacos.jpg', 'main-course', 'Mexican Grill'),
('Burrito Bowl', 'UGX 400', 'burrito.jpg', 'main-course', 'Mexican Grill'),
('Churros', 'UGX 180', 'churros.jpg', 'dessert', 'Mexican Grill'),
('Horchata', 'UGX 110', 'horchata.jpg', 'beverage', 'Mexican Grill'),
('Shrimp Cocktail', 'UGX 300', 'shrimp-cocktail.jpg', 'appetizer', 'Seafood Delight'),
('Calamari', 'UGX 350', 'calamari.jpg', 'appetizer', 'Seafood Delight'),
('Grilled Lobster', 'UGX 800', 'lobster.jpg', 'main-course', 'Seafood Delight'),
('Fish Tacos', 'UGX 450', 'fish-tacos.jpg', 'main-course', 'Seafood Delight'),
('Key Lime Pie', 'UGX 220', 'key-lime.jpg', 'dessert', 'Seafood Delight'),
('Seafood Paella', 'UGX 600', 'paella.jpg', 'main-course', 'Seafood Delight'),
('Veggie Spring Rolls', 'UGX 170', 'veggie-rolls.jpg', 'appetizer', 'Vegan Paradise'),
('Hummus Platter', 'UGX 250', 'hummus.jpg', 'appetizer', 'Vegan Paradise'),
('Vegan Burger', 'UGX 380', 'vegan-burger.jpg', 'main-course', 'Vegan Paradise'),
('Quinoa Salad', 'UGX 320', 'quinoa.jpg', 'main-course', 'Vegan Paradise'),
('Vegan Chocolate Cake', 'UGX 210', 'vegan-cake.jpg', 'dessert', 'Vegan Paradise'),
('Almond Milk Latte', 'UGX 130', 'almond-latte.jpg', 'beverage', 'Vegan Paradise'),
('French Fries', 'UGX 150', 'fries.jpg', 'appetizer', 'Fast Food Hub'),
('Chicken Nuggets', 'UGX 250', 'nuggets.jpg', 'appetizer', 'Fast Food Hub'),
('Big Mac', 'UGX 400', 'big-mac.jpg', 'main-course', 'Fast Food Hub'),
('Chicken Sandwich', 'UGX 350', 'chicken-sandwich.jpg', 'main-course', 'Fast Food Hub'),
('Milkshake', 'UGX 180', 'milkshake.jpg', 'dessert', 'Fast Food Hub'),
('Soda', 'UGX 70', 'soda.jpg', 'beverage', 'Fast Food Hub'),
('Macarons', 'UGX 150', 'macarons.jpg', 'dessert', 'Dessert Heaven'),
('Profiteroles', 'UGX 200', 'profiteroles.jpg', 'dessert', 'Dessert Heaven'),
('Panna Cotta', 'UGX 180', 'panna-cotta.jpg', 'dessert', 'Dessert Heaven'),
('Gelato', 'UGX 160', 'gelato.jpg', 'dessert', 'Dessert Heaven'),
('Hot Chocolate', 'UGX 120', 'hot-choco.jpg', 'beverage', 'Dessert Heaven');
