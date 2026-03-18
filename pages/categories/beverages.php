<?php
session_start();
require '../../includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$pageTitle = "Beverages - Smart Dine";
$headerTitle = "Smart Dine";

$products = json_decode(file_get_contents('../../data/products.json'), true);
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : null;

$results = array_filter($products, function ($product) use ($restaurant) {
    return $product['category'] === 'beverage' && (!$restaurant || $product['restaurant'] === $restaurant);
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
        <h1 class="page-title">Beverages</h1>
        <p class="page-description">
            Quench your thirst with our refreshing beverage selection. From classic favorites to specialty drinks, 
            we have the perfect complement to your meal.
        </p>
    </div>

    <?php if (empty($results)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="ri-cup-line" style="font-size:3rem;color:#ccc;"></i></div>
            <h2 class="empty-state-title">No Beverages Found</h2>
            <p class="empty-state-description">
                We couldn't find any beverages<?php echo $restaurant ? ' from ' . htmlspecialchars($restaurant) : ''; ?>. 
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
                        <div class="product-category">Beverage</div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-restaurant">
                            <span style="color: #999;">from</span> <?php echo htmlspecialchars($product['restaurant']); ?>
                        </p>
                        <div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div>
                        <button class="btn btn-primary btn-full"
                                type="button"
                                data-add-to-cart
                                data-product-id="<?php echo (int)$product['id']; ?>"
                                data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-product-price="<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-restaurant="<?php echo htmlspecialchars($product['restaurant'], ENT_QUOTES, 'UTF-8'); ?>">
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
