<?php
session_start();
require 'includes/connect.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : null;

try {
    $products = json_decode(file_get_contents('data/products.json'), true);
    if ($products === null) {
        throw new Exception('Invalid JSON in products.json');
    }
} catch (Exception $e) {
    die('Error loading products: ' . $e->getMessage());
}

// Debug: Check if products loaded
if (empty($products)) {
    die('No products found in JSON file.');
}

// Add IDs to all products globally
foreach ($products as $index => $product) {
    if (!isset($product['id'])) {
        $products[$index]['id'] = $index + 1;
    }
}

if ($restaurant) {
    // Filter products for the selected restaurant
    $results = array_filter($products, function ($product) use ($restaurant) {
        return $product['restaurant'] === $restaurant;
    });
    $pageTitle = "$restaurant - Smart Dine";
    $headerTitle = "$restaurant - Delicious Food Awaits";
} else {
    $restaurants = array_unique(array_column($products, 'restaurant'));
    $pageTitle = "Smart Dine - Multi-Restaurant Platform";
    $headerTitle = "Smart Dine - Choose Your Restaurant";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body >
    <header>
        <h1><?php echo $headerTitle; ?></h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <?php if ($restaurant): ?>
                    <li><a href="appetizers.php?restaurant=<?php echo urlencode($restaurant); ?>">Appetizers</a></li>
                    <li><a href="main-courses.php?restaurant=<?php echo urlencode($restaurant); ?>">Main Courses</a></li>
                    <li><a href="desserts.php?restaurant=<?php echo urlencode($restaurant); ?>">Desserts</a></li>
                    <li><a href="beverages.php?restaurant=<?php echo urlencode($restaurant); ?>">Beverages</a></li>
                <?php endif; ?>
                <li><a href="search.php">Search Dishes</a></li>
                <li><a href="cart.php">Cart (<span id="cart-count">0</span>)</a></li>
                <li><a href="checkout.php">Checkout</a></li>
                <li><a href="logout.php" style="color: #ff6f61;">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if ($restaurant): ?>
            <section id="featured-products">
                <h2>Menu</h2>
                <div id="featured-products" class="product-grid">
                    <?php foreach ($results as $product): ?>
                        <div class="product">
                            <img src="assets/images/food pics/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                            <h3><?php echo $product['name']; ?></h3>
                            <p><?php echo $product['price']; ?></p>
                            <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>', '<?php echo addslashes($product['restaurant']); ?>')">Add to Cart</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <section id="restaurants">
                <h2>Our Restaurants</h2>
                <div class="restaurant-grid">
                    <?php foreach ($restaurants as $rest): ?>
                        <div class="restaurant">
                            <h3><?php echo $rest; ?></h3>
                            <p>Explore delicious dishes from <?php echo $rest; ?>.</p>
                            <a href="home.php?restaurant=<?php echo urlencode($rest); ?>" class="view-menu-btn">View Menu</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>Contact us: +123-456-7890 | Smart Dine - Taste the Difference</p>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>

