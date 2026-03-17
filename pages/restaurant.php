<?php
session_start();
require '../includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: pages/auth/login.php');
    exit();
}

$name = isset($_GET['name']) ? trim($_GET['name']) : '';
if (!$name) { header('Location: ../home.php'); exit(); }

// Load restaurant info from DB, fall back to defaults
$restaurantInfo = null;
try {
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE name = ?");
    $stmt->execute([$name]);
    $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Load products from JSON
$allProducts = json_decode(file_get_contents('../data/products.json'), true);
$products = array_values(array_filter($allProducts, fn($p) => $p['restaurant'] === $name));

// Load reviews
$reviews = [];
$avgRating = 0;
try {
    $stmt = $conn->prepare("
        SELECT r.*, u.username FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.restaurant = ? ORDER BY r.created_at DESC
    ");
    $stmt->execute([$name]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($reviews) {
        $avgRating = round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1);
    }
} catch (Exception $e) {}

// Handle review submission
$reviewMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating  = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    if ($rating >= 1 && $rating <= 5) {
        try {
            $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, restaurant, rating, comment) VALUES (?, 0, ?, ?, ?) ON DUPLICATE KEY UPDATE rating=VALUES(rating), comment=VALUES(comment)");
            $stmt->execute([$_SESSION['user_id'], $name, $rating, $comment]);
            header("Location: restaurant.php?name=" . urlencode($name) . "&reviewed=1");
            exit();
        } catch (Exception $e) {
            $reviewMsg = 'Could not save review: ' . $e->getMessage();
        }
    }
}

$pageTitle = htmlspecialchars($name) . " - SmartDine Hub";
include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/elegant-theme.css">
<style>
.rest-wrap { max-width:1100px; margin:0 auto; padding:30px 20px; }
.rest-hero {
    background: linear-gradient(rgba(0,0,0,0.45),rgba(0,0,0,0.55)),
        url('../assets/images/food pics/<?php echo htmlspecialchars($restaurantInfo['image'] ?? 'Beef Steak.jpg'); ?>') center/cover;
    border-radius:20px; padding:60px 40px; color:white; margin-bottom:40px;
}
.rest-hero h1 { font-family:'Playfair Display',serif; font-size:2.8em; margin-bottom:10px; }
.rest-hero .meta { display:flex; gap:20px; flex-wrap:wrap; margin-top:15px; font-size:0.95em; opacity:0.9; }
.rest-hero .meta span { background:rgba(255,255,255,0.2); padding:6px 16px; border-radius:20px; }
.info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:40px; }
.info-card { background:white; border-radius:16px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.07); border:1px solid #f0f0f0; text-align:center; }
.info-card .icon { font-size:2em; margin-bottom:10px; }
.info-card h4 { font-size:0.8em; text-transform:uppercase; letter-spacing:1px; color:#999; margin-bottom:6px; }
.info-card p { font-weight:600; color:#333; }
.stars { color:#f5a623; font-size:1.2em; }
.section-title { font-family:'Playfair Display',serif; font-size:1.8em; color:#333; margin-bottom:25px; }
.review-card { background:white; border-radius:16px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.06); border:1px solid #f0f0f0; margin-bottom:15px; }
.review-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.reviewer { font-weight:600; color:#333; }
.review-date { font-size:0.85em; color:#aaa; }
.review-comment { color:#555; line-height:1.6; }
.review-form { background:white; border-radius:16px; padding:30px; box-shadow:0 4px 15px rgba(0,0,0,0.07); border:1px solid #f0f0f0; margin-bottom:40px; }
.star-select { display:flex; gap:8px; margin-bottom:15px; }
.star-select input[type=radio] { display:none; }
.star-select label { font-size:2em; cursor:pointer; color:#ddd; transition:color 0.2s; }
.star-select input[type=radio]:checked ~ label,
.star-select label:hover,
.star-select label:hover ~ label { color:#f5a623; }
.star-select { flex-direction:row-reverse; justify-content:flex-end; }
textarea.form-control { resize:vertical; min-height:100px; }
.success-msg { background:#e8f5e9; color:#2e7d32; padding:12px 18px; border-radius:10px; border-left:4px solid #4caf50; margin-bottom:20px; }
</style>

<div class="rest-wrap">
    <div class="rest-hero">
        <h1><?php echo htmlspecialchars($name); ?></h1>
        <?php if ($restaurantInfo): ?>
        <p style="font-size:1.1em; opacity:0.9; max-width:600px;"><?php echo htmlspecialchars($restaurantInfo['description']); ?></p>
        <div class="meta">
            <span>🍽️ <?php echo htmlspecialchars($restaurantInfo['cuisine_type']); ?></span>
            <span>📍 <?php echo htmlspecialchars($restaurantInfo['address']); ?></span>
            <span>🕐 <?php echo htmlspecialchars($restaurantInfo['opening_hours']); ?></span>
            <?php if ($avgRating > 0): ?>
            <span>⭐ <?php echo $avgRating; ?>/5 (<?php echo count($reviews); ?> reviews)</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Info cards -->
    <?php if ($restaurantInfo): ?>
    <div class="info-grid">
        <div class="info-card"><div class="icon">📞</div><h4>Phone</h4><p><?php echo htmlspecialchars($restaurantInfo['phone']); ?></p></div>
        <div class="info-card"><div class="icon">🕐</div><h4>Hours</h4><p><?php echo htmlspecialchars($restaurantInfo['opening_hours']); ?></p></div>
        <div class="info-card"><div class="icon">📍</div><h4>Location</h4><p><?php echo htmlspecialchars($restaurantInfo['region']); ?> Region</p></div>
        <div class="info-card"><div class="icon">⭐</div><h4>Rating</h4>
            <p><?php echo $avgRating > 0 ? $avgRating . '/5' : 'No ratings yet'; ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Products -->
    <h2 class="section-title">Our Menu</h2>
    <?php if (empty($products)): ?>
        <p style="color:#999;">No products available for this restaurant.</p>
    <?php else: ?>
    <div class="product-grid" style="margin-bottom:50px;">
        <?php foreach ($products as $product):
            $inStock = $product['in_stock'] ?? 1;
        ?>
        <div class="product-card" style="<?php echo !$inStock ? 'opacity:0.6;' : ''; ?>">
            <div class="product-image" style="position:relative;">
                <img src="../assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>"
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php if (!$inStock): ?>
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;border-radius:inherit;">
                    <span style="color:white;font-weight:700;font-size:1.1em;">Out of Stock</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                <?php if (!empty($product['description'])): ?>
                <p style="color:#888;font-size:0.85em;line-height:1.5;margin-bottom:10px;"><?php echo htmlspecialchars($product['description']); ?></p>
                <?php endif; ?>
                <div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div>
                <?php if ($inStock): ?>
                <button class="btn btn-primary btn-full" style="margin-top:12px;"
                    type="button"
                    data-add-to-cart
                    data-product-id="<?php echo (int)$product['id']; ?>"
                    data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-product-price="<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-restaurant="<?php echo htmlspecialchars($product['restaurant'], ENT_QUOTES, 'UTF-8'); ?>">
                    Add to Cart
                </button>
                <?php else: ?>
                <button class="btn btn-full" style="margin-top:12px;background:#ccc;cursor:not-allowed;" disabled>Out of Stock</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Review form -->
    <h2 class="section-title">Leave a Review</h2>
    <?php if (isset($_GET['reviewed'])): ?>
        <div class="success-msg">✅ Thanks for your review!</div>
    <?php endif; ?>
    <?php if ($reviewMsg): ?>
        <div style="background:#ffebee;color:#c62828;padding:12px 18px;border-radius:10px;border-left:4px solid #f44336;margin-bottom:20px;"><?php echo $reviewMsg; ?></div>
    <?php endif; ?>
    <div class="review-form">
        <form method="POST">
            <div class="form-group">
                <label style="font-weight:600;display:block;margin-bottom:8px;">Your Rating</label>
                <div class="star-select">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                    <label for="star<?php echo $i; ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="form-group">
                <label style="font-weight:600;display:block;margin-bottom:8px;">Comment (optional)</label>
                <textarea name="comment" class="form-control" placeholder="Tell others about your experience..."></textarea>
            </div>
            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
        </form>
    </div>

    <!-- Existing reviews -->
    <?php if ($reviews): ?>
    <h2 class="section-title">Reviews (<?php echo count($reviews); ?>)</h2>
    <?php foreach ($reviews as $review): ?>
    <div class="review-card">
        <div class="review-header">
            <span class="reviewer">👤 <?php echo htmlspecialchars($review['username']); ?></span>
            <span class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
        </div>
        <div class="stars"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
        <?php if ($review['comment']): ?>
        <p class="review-comment" style="margin-top:8px;"><?php echo htmlspecialchars($review['comment']); ?></p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p style="color:#aaa;">No reviews yet. Be the first!</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script>
// Fix star rating CSS direction
document.querySelectorAll('.star-select label').forEach(label => {
    label.addEventListener('mouseover', function() {
        let el = this;
        while (el) { if (el.tagName === 'LABEL') el.style.color = '#f5a623'; el = el.nextElementSibling; }
    });
});
</script>
