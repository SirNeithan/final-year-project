<?php
session_start();
// Use the products.json file for products data
$products = json_decode(file_get_contents('data/products.json'), true);
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : null;

// Filter products for main courses
$results = array_filter($products, function ($product) use ($restaurant) {
    return $product['category'] === 'main-course' && (!$restaurant || $product['restaurant'] === $restaurant);
});

// Add IDs to products if they don't have them
foreach ($results as $index => $product) {
    if (!isset($product['id'])) {
        $results[$index]['id'] = $index + 1;  // Add sequential ID
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Courses - Smart Dine</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product img {
            max-width: 100%;
            height: auto;
            max-height: 150px;
            object-fit: contain;
        }
        .product button {
            background-color: #ff6b35;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .product button:hover {
            background-color: #e55a2b;
        }
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Main Courses</h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="search.php">Search Dishes</a></li>
                <li><a href="cart.php">Cart (<span id="cart-count">0</span>)</a></li>
                <li><a href="checkout.php">Checkout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Our Main Courses</h2>
            <div class="product-grid">
                <?php foreach ($results as $product): ?>
                    <div class="product">
                        <img src="assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='data:image/svg+xml;...'">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['price']; ?></p>
                        <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo addslashes($product['price']); ?>', '<?php echo addslashes($product['restaurant']); ?>')">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>Contact us: 0766191751 | Smart Dine - Taste the Difference</p>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>

