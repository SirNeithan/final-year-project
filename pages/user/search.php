<?php
session_start();
$products = json_decode(file_get_contents('../../data/products.json'), true);

$regionMap = [
    'Central'  => ['Smart Dine', 'Italian Corner', 'Dessert Heaven'],
    'Eastern'  => ['Asian Fusion', 'Fast Food Hub'],
    'Western'  => ['Seafood Delight', 'Vegan Paradise'],
    'Northern' => ['Mexican Grill'],
];

$query      = isset($_GET['query'])      ? strtolower($_GET['query']) : '';
$category   = isset($_GET['category'])   ? $_GET['category']          : '';
$region     = isset($_GET['region'])     ? $_GET['region']             : '';
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant']         : '';

// If a region is selected, restrict to its restaurants
$allowedRestaurants = (!empty($region) && isset($regionMap[$region])) ? $regionMap[$region] : [];

$results = array_filter($products, function ($product) use ($query, $category, $restaurant, $allowedRestaurants) {
    $matchesQuery      = empty($query)      || strpos(strtolower($product['name']), $query) !== false;
    $matchesCategory   = empty($category)   || $product['category']   === $category;
    $matchesRestaurant = empty($restaurant) || $product['restaurant'] === $restaurant;
    $matchesRegion     = empty($allowedRestaurants) || in_array($product['restaurant'], $allowedRestaurants);
    return $matchesQuery && $matchesCategory && $matchesRestaurant && $matchesRegion;
});

foreach ($results as $index => $product) {
    if (!isset($product['id'])) {
        $results[$index]['id'] = $index + 1;
    }
}

$pageTitle = "Search - SmartDine Hub";
$headerTitle = "SmartDine Hub";
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
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}

.search-row .search-input {
    flex: 2 1 200px;
    min-width: 0;
}

.search-row .search-select {
    flex: 1 1 130px;
    min-width: 0;
}

.search-row .search-btn {
    flex: 0 0 auto;
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

@media (max-width: 600px) {
    .search-row .search-btn {
        width: 100%;
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
                    <select name="region" id="region-select" class="search-select" onchange="filterRestaurantsByRegion()">
                        <option value="">All Regions</option>
                        <?php foreach (array_keys($regionMap) as $r): ?>
                            <option value="<?php echo $r; ?>" <?php echo $region === $r ? 'selected' : ''; ?>>
                                <?php echo $r; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin: 0;">
                    <select name="restaurant" id="restaurant-select" class="search-select">
                        <option value="">All Restaurants</option>
                        <?php
                        $allRestaurants = array_unique(array_column($products, 'restaurant'));
                        foreach ($allRestaurants as $rest):
                            // Find which region this restaurant belongs to
                            $restRegion = '';
                            foreach ($regionMap as $rName => $rRests) {
                                if (in_array($rest, $rRests)) { $restRegion = $rName; break; }
                            }
                        ?>
                            <option value="<?php echo htmlspecialchars($rest); ?>"
                                    data-region="<?php echo $restRegion; ?>"
                                    <?php echo $restaurant === $rest ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rest); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="search-btn">Search</button>
            </div>
        </form>

        <script>
        function filterRestaurantsByRegion() {
            const region = document.getElementById('region-select').value;
            const select = document.getElementById('restaurant-select');
            select.value = '';
            Array.from(select.options).forEach(opt => {
                if (!opt.value) return; // keep "All Restaurants"
                opt.hidden = region && opt.dataset.region !== region;
            });
        }
        // Run on page load to respect pre-selected region
        filterRestaurantsByRegion();
        </script>
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
