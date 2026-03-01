<?php
session_start();
require '../../includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$pageTitle = "Desserts - Smart Dine";
$headerTitle = "Smart Dine";

$products = json_decode(file_get_contents('../../data/products.json'), true);
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : null;

$results = array_filter($products, function ($product) use ($restaurant) {
    return $product['category'] === 'dessert' && (!$restaurant || $product['restaurant'] === $restaurant);
});

foreach ($results as $index => $product) {
    if (!isset($product['id'])) {
        $results[$index]['id'] = $index + 1;
    }
}

include '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/elegant-theme.css">

<div class="page-container">
    <div class="page-header">
        <div class="page-subtitle">Our Menu</div>
        <h1 class="page-title">Desserts</h1>
        <p class="page-description">
            Satisfy your sweet tooth with our exquisite dessert collection. From decadent cakes to refreshing treats, 
            end your meal on a perfect note.
        </p>
    </div>

    <?php if (empty($results)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🍰</div>
            <h2 class="empty-state-title">No Desserts Found</h2>
            <p class="empty-state-description">
                We couldn't find any desserts<?php echo $restaurant ? ' from ' . htmlspecialchars($restaurant) : ''; ?>. 
                Please check back later or explore other categories.
            </p>
            <a href="../../home.php" class="btn btn-primary">Back to Home</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($results as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="../../assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='../../assets/images/placeholder.jpg'">
                        <?php if ($product['restaurant']): ?>
                            <div class="product-badge"><?php echo htmlspecialchars($product['restaurant']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Dessert</div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-restaurant">
                            <span style="color: #999;">from</span> <?php echo htmlspecialchars($product['restaurant']); ?>
                        </p>
                        <div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div>
                        <button class="btn btn-primary btn-full" 
                                onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo htmlspecialchars($product['price']); ?>', '<?php echo addslashes($product['restaurant']); ?>')">
                            Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>

<script src="../../assets/js/script.js"></script>
