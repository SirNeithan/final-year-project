<?php
session_start();
require '../../includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$pageTitle = "Main Courses - Smart Dine";
$headerTitle = "Smart Dine";

$products = json_decode(file_get_contents('../../data/products.json'), true);
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : null;

$results = array_filter($products, function ($product) use ($restaurant) {
    return $product['category'] === 'main-course' && (!$restaurant || $product['restaurant'] === $restaurant);
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
        <h1 class="page-title">Main Courses</h1>
        <p class="page-description">
            Indulge in our carefully curated selection of main courses. From hearty classics to contemporary cuisine, 
            every dish is prepared with passion and the finest ingredients.
        </p>
    </div>

    <?php if (empty($results)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🍽️</div>
            <h2 class="empty-state-title">No Main Courses Found</h2>
            <p class="empty-state-description">
                We couldn't find any main courses<?php echo $restaurant ? ' from ' . htmlspecialchars($restaurant) : ''; ?>. 
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
                        <div class="product-category">Main Course</div>
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
