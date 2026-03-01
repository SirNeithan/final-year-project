-- Update Product Prices to New Range (20,000 - 80,000 UGX)
-- Run this script if you already have products in your database

USE smartdine;

-- Update prices for existing products
-- This matches the products from products.json

UPDATE products SET price = 'UGX 25,000' WHERE name = 'Bruschetta';
UPDATE products SET price = 'UGX 20,000' WHERE name = 'Garlic Bread';
UPDATE products SET price = 'UGX 30,000' WHERE name = 'Caesar Salad';
UPDATE products SET price = 'UGX 35,000' WHERE name = 'Chicken Wings';
UPDATE products SET price = 'UGX 45,000' WHERE name = 'Cheeseburger';
UPDATE products SET price = 'UGX 50,000' WHERE name = 'Grilled Chicken Burger';
UPDATE products SET price = 'UGX 75,000' WHERE name = 'Beef Steak';
UPDATE products SET price = 'UGX 65,000' WHERE name = 'Chicken Parmesan';
UPDATE products SET price = 'UGX 28,000' WHERE name = 'Chocolate Cake';
UPDATE products SET price = 'UGX 32,000' WHERE name = 'Cheesecake';
UPDATE products SET price = 'UGX 22,000' WHERE name = 'Fruit Salad';
UPDATE products SET price = 'UGX 20,000' WHERE name = 'Coca Cola';
UPDATE products SET price = 'UGX 25,000' WHERE name = 'Coffee';
UPDATE products SET price = 'UGX 30,000' WHERE name = 'Caprese Salad';
UPDATE products SET price = 'UGX 45,000' WHERE name = 'Antipasto Platter';
UPDATE products SET price = 'UGX 22,000' WHERE name = 'Espresso';
UPDATE products SET price = 'UGX 35,000' WHERE name = 'Dumplings';
UPDATE products SET price = 'UGX 28,000' WHERE name = 'Green Tea Ice Cream';
UPDATE products SET price = 'UGX 25,000' WHERE name = 'Bubble Tea';
UPDATE products SET price = 'UGX 55,000' WHERE name = 'Burrito Bowl';
UPDATE products SET price = 'UGX 26,000' WHERE name = 'Churros';
UPDATE products SET price = 'UGX 42,000' WHERE name = 'Calamari';
UPDATE products SET price = 'UGX 80,000' WHERE name = 'Grilled Lobster';
UPDATE products SET price = 'UGX 48,000' WHERE name = 'Fish Tacos';
UPDATE products SET price = 'UGX 20,000' WHERE name = 'French Fries';
UPDATE products SET price = 'UGX 32,000' WHERE name = 'Chicken Nuggets';
UPDATE products SET price = 'UGX 50,000' WHERE name = 'Big Mac';
UPDATE products SET price = 'UGX 45,000' WHERE name = 'Chicken Sandwich';
UPDATE products SET price = 'UGX 24,000' WHERE name = 'Gelato';
UPDATE products SET price = 'UGX 28,000' WHERE name = 'Almond Milk Latte';

-- Update any other products that might exist
-- These are from the old SQL file but not in products.json
UPDATE products SET price = 'UGX 22,000' WHERE name = 'Mozzarella Sticks';
UPDATE products SET price = 'UGX 55,000' WHERE name = 'Margherita Pizza';
UPDATE products SET price = 'UGX 60,000' WHERE name = 'Pepperoni Pizza';
UPDATE products SET price = 'UGX 65,000' WHERE name = 'Pasta Carbonara';
UPDATE products SET price = 'UGX 58,000' WHERE name = 'Spaghetti Bolognese';
UPDATE products SET price = 'UGX 70,000' WHERE name = 'Grilled Salmon';
UPDATE products SET price = 'UGX 45,000' WHERE name = 'Vegetable Stir Fry';
UPDATE products SET price = 'UGX 30,000' WHERE name = 'Tiramisu';
UPDATE products SET price = 'UGX 26,000' WHERE name = 'Ice Cream Sundae';
UPDATE products SET price = 'UGX 22,000' WHERE name = 'Orange Juice';
UPDATE products SET price = 'UGX 20,000' WHERE name = 'Iced Tea';
UPDATE products SET price = 'UGX 20,000' WHERE name = 'Mineral Water';
UPDATE products SET price = 'UGX 62,000' WHERE name = 'Lasagna';
UPDATE products SET price = 'UGX 58,000' WHERE name = 'Risotto';
UPDATE products SET price = 'UGX 25,000' WHERE name = 'Spring Rolls';
UPDATE products SET price = 'UGX 52,000' WHERE name = 'Kung Pao Chicken';
UPDATE products SET price = 'UGX 50,000' WHERE name = 'Pad Thai';
UPDATE products SET price = 'UGX 35,000' WHERE name = 'Nachos';
UPDATE products SET price = 'UGX 38,000' WHERE name = 'Quesadillas';
UPDATE products SET price = 'UGX 42,000' WHERE name = 'Tacos';
UPDATE products SET price = 'UGX 24,000' WHERE name = 'Horchata';
UPDATE products SET price = 'UGX 38,000' WHERE name = 'Shrimp Cocktail';
UPDATE products SET price = 'UGX 28,000' WHERE name = 'Key Lime Pie';
UPDATE products SET price = 'UGX 72,000' WHERE name = 'Seafood Paella';
UPDATE products SET price = 'UGX 24,000' WHERE name = 'Veggie Spring Rolls';
UPDATE products SET price = 'UGX 32,000' WHERE name = 'Hummus Platter';
UPDATE products SET price = 'UGX 48,000' WHERE name = 'Vegan Burger';
UPDATE products SET price = 'UGX 40,000' WHERE name = 'Quinoa Salad';
UPDATE products SET price = 'UGX 28,000' WHERE name = 'Vegan Chocolate Cake';
UPDATE products SET price = 'UGX 26,000' WHERE name = 'Milkshake';
UPDATE products SET price = 'UGX 20,000' WHERE name = 'Soda';
UPDATE products SET price = 'UGX 22,000' WHERE name = 'Macarons';
UPDATE products SET price = 'UGX 26,000' WHERE name = 'Profiteroles';
UPDATE products SET price = 'UGX 25,000' WHERE name = 'Panna Cotta';
UPDATE products SET price = 'UGX 24,000' WHERE name = 'Hot Chocolate';

-- Verify the updates
SELECT name, price, category, restaurant FROM products ORDER BY category, name;
