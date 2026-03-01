-- QUICK FIX: Update all product prices to new range
-- Just run this file to fix the price display issue
-- Command: mysql -u root -p smartdine < QUICK_FIX.sql

USE smartdine;

-- Main products from products.json (30 items)
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

-- Show updated prices
SELECT 'PRICES UPDATED SUCCESSFULLY!' as Status;
SELECT name, price, category FROM products ORDER BY category, name LIMIT 10;
