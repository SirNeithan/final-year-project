<?php
session_start();
require 'includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Main Courses - Smart Dine";
$headerTitle = "🍽️ Main Courses";

$products = json_decode(file_get_contents('data/products.json'), true);
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : null;

$results = array_filter($products, function ($product) use ($restaurant) {
    return $product['category'] === 'main-course' && (!$restaurant || $product['restaurant'] === $restaurant);
});

foreach ($results as $index => $product) {
    if (!isset($product['id'])) {
        $results[$index]['id'] = $index + 1;
    }
}

if ($restaurant) {
    $headerTitle = "🍽️ Main Courses - " . htmlspecialchars($restaurant);
}

include 'includes/header.php';
?>

<style>
    .hero-section {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 50px;
        text-align: center;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }
    
    .hero-section h2 {
        font-size: 3em;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .hero-section p {
        font-size: 1.3em;
        color: #333;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    
    .product {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .product:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }
    
    .product img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
    
    .product-info {
        padding: 25px;
    }
    
    .product h3 {
        font-size: 1.4em;
        color: #333;
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .product-restaurant {
        color: #333;
        font-size: 0.9em;
        margin-bottom: 10px;
    }
    
    .product p {
        color: #667eea;
        font-size: 1.5em;
        font-weight: 700;
        margin: 15px 0;
    }
    
    .product button {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 1.05em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .product button:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
    }
</style>

<div class="hero-section">
    <h2><?php echo $headerTitle; ?></h2>
    <p>Satisfy your hunger with our hearty main courses</p>
    <?php if ($restaurant): ?>
        <p style="margin-top: 15px;"><a href="main-courses.php" style="color: #667eea; font-weight: 600;">View all restaurants</a></p>
    <?php endif; ?>
</div>

<div class="product-grid">
    <?php if (empty($results)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: rgba(255,255,255,0.95); border-radius: 20px;">
            <h3 style="color: #667eea;">No main courses found</h3>
        </div>
    <?php else: ?>
        <?php foreach ($results as $product): ?>
            <div class="product">
                <img src="assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-restaurant">🏪 <?php echo htmlspecialchars($product['restaurant']); ?></div>
                    <p><?php echo htmlspecialchars($product['price']); ?></p>
                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo addslashes($product['price']); ?>', '<?php echo addslashes($product['restaurant']); ?>')">
                        🛒 Add to Cart
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
