<?php
session_start();
$products = json_decode(file_get_contents('../../data/products.json'), true);

$query = isset($_GET['query']) ? strtolower($_GET['query']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : '';

$results = array_filter($products, function ($product) use ($query, $category, $restaurant) {
    $matchesQuery = empty($query) || strpos(strtolower($product['name']), $query) !== false;
    $matchesCategory = empty($category) || $product['category'] === $category;
    $matchesRestaurant = empty($restaurant) || $product['restaurant'] === $restaurant;
    return $matchesQuery && $matchesCategory && $matchesRestaurant;
});

foreach ($results as $index => $product) {
    if (!isset($product['id'])) {
        $results[$index]['id'] = $index + 1;
    }
}

$pageTitle = "Search - Smart Dine";
$headerTitle = "Smart Dine";
include '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/elegant-theme.css">

<style>
.search-hero {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    padding: 60px 20px 40px;
    border-radius: 20px;
    margin-bottom: 40px;
}

.search-form {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.search-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 15px;
    align-items: end;
}

.search-input {
    padding: 18px 25px;
    border: 2px solid #e8e8e8;
    border-radius: 50px;
    font-size: 1.1em;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.search-select {
    padding: 18px 25px;
    border: 2px solid #e8e8e8;
    border-radius: 50px;
    font-size: 1.05em;
    font-family: 'Poppins', sans-serif;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
}

.search-select:focus {
    outline: none;
    border-color: #667eea;
}

.search-btn {
    padding: 18px 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1em;
    cursor: pointer;
    transition: all 0.3s;
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.results-count {
    font-size: 1.2em;
    color: #666;
}

.results-count strong {
    color: #667eea;
    font-size: 1.3em;
}

@media (max-width: 968px) {
    .search-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-container">
    <div class="search-hero">
        <div class="page-header" style="background: none; padding: 0;">
            <div class="page-subtitle">Discover</div>
            <h1 class="page-title">Search Our Menu</h1>
            <p class="page-description">
                Find your favorite dishes from our extensive collection of restaurants
            </p>
        </div>
        
        <form method="GET" action="" class="search-form">
            <div class="search-row">
                <div class="form-group" style="margin: 0;">
                    <input type="text" 
                           name="query" 
                           class="search-input" 
                           placeholder="Search for dishes..." 
                           value="<?php echo htmlspecialchars($query); ?>">
                </div>
                
                <div class="form-group" style="margin: 0;">
                    <select name="category" class="search-select">
                        <option value="">All Categories</option>
                        <option value="appetizer" <?php echo $category === 'appetizer' ? 'selected' : ''; ?>>Appetizers</option>
                        <option value="main-course" <?php echo $category === 'main-course' ? 'selected' : ''; ?>>Main Courses</option>
                        <option value="dessert" <?php echo $category === 'dessert' ? 'selected' : ''; ?>>Desserts</option>
                        <option value="beverage" <?php echo $category === 'beverage' ? 'selected' : ''; ?>>Beverages</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin: 0;">
                    <select name="restaurant" class="search-select">
                        <option value="">All Restaurants</option>
                        <?php
                        $restaurants = array_unique(array_column($products, 'restaurant'));
                        foreach ($restaurants as $rest):
                        ?>
                            <option value="<?php echo htmlspecialchars($rest); ?>" <?php echo $restaurant === $rest ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rest); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="search-btn">Search</button>
            </div>
        </form>
    </div>

    <?php if ($query || $category || $restaurant): ?>
        <div class="results-header">
            <div class="results-count">
                Found <strong><?php echo count($results); ?></strong> <?php echo count($results) === 1 ? 'dish' : 'dishes'; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($results) && ($query || $category || $restaurant)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h2 class="empty-state-title">No Results Found</h2>
            <p class="empty-state-description">
                We couldn't find any dishes matching your search. Try different keywords or filters.
            </p>
            <a href="search.php" class="btn btn-primary">Clear Search</a>
        </div>
    <?php elseif (empty($results)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🍽️</div>
            <h2 class="empty-state-title">Start Searching</h2>
            <p class="empty-state-description">
                Use the search bar above to find your favorite dishes from our menu
            </p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($results as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="../../assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-badge"><?php echo htmlspecialchars($product['restaurant']); ?></div>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo ucfirst(str_replace('-', ' ', $product['category'])); ?></div>
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
