<?php
session_start();
require 'includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: pages/auth/login.php');
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

if (empty($products)) {
    die('No products found in JSON file.');
}

foreach ($products as $index => $product) {
    if (!isset($product['id'])) {
        $products[$index]['id'] = $index + 1;
    }
}

$regionMap = [
    'Central'  => ['Smart Dine', 'Italian Corner', 'Dessert Heaven'],
    'Eastern'  => ['Asian Fusion', 'Fast Food Hub'],
    'Western'  => ['Seafood Delight', 'Vegan Paradise'],
    'Northern' => ['Mexican Grill'],
];

$restaurantImages = [
    'Smart Dine' => 'Cheeseburger.jpg',
    'Italian Corner' => 'Antipasto Platter.jpg',
    'Asian Fusion' => 'Dumplings.jpg',
    'Mexican Grill' => 'Burrito Bowl.jpg',
    'Seafood Delight' => 'Grilled Lobster.jpg',
    'Fast Food Hub' => 'Big Mac.jpg',
    'Dessert Heaven' => 'Cheesecake.jpg',
    'Vegan Paradise' => 'Almond Milk Latte.jpg'
];

$restaurantDescriptions = [
    'Smart Dine' => 'Classic comfort food and international favorites',
    'Italian Corner' => 'Authentic Italian cuisine with a modern twist',
    'Asian Fusion' => 'Bold Asian flavors from across the continent',
    'Mexican Grill' => 'Spicy and flavorful Mexican specialties',
    'Seafood Delight' => 'Fresh seafood prepared to perfection',
    'Fast Food Hub' => 'Quick bites and satisfying meals',
    'Dessert Heaven' => 'Sweet treats and decadent desserts',
    'Vegan Paradise' => 'Plant-based delights for conscious eaters'
];

if ($restaurant) {
    $results = array_filter($products, function ($product) use ($restaurant) {
        return $product['restaurant'] === $restaurant;
    });
    $pageTitle = "$restaurant - SmartDine Hub";
    $headerTitle = "SmartDine Hub";
} else {
    $restaurants = array_unique(array_column($products, 'restaurant'));
    $pageTitle = "SmartDine Hub - Multi-Restaurant Platform";
    $headerTitle = "SmartDine Hub";
}
?>
<?php include 'includes/header.php'; ?>
    <style>
        body {
            background: #ffffff;
        }
        
        /* Hero Section */
        .hero-section {
            position: relative;
            height: 85vh;
            min-height: 600px;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.4)), 
                        url('assets/images/food pics/Beef Steak.jpg') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-top: -20px;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 40px;
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1em;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #f0f0f0;
            font-weight: 300;
        }
        
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 5em;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 25px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }
        
        .hero-description {
            font-size: 1.3em;
            margin-bottom: 40px;
            line-height: 1.6;
            color: #f5f5f5;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            padding: 18px 45px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            display: inline-block;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.6);
        }
        
        .btn-secondary {
            padding: 18px 45px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #333;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* About Section */
        .about-section {
            padding: 100px 20px;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        
        .about-image {
            position: relative;
        }
        
        .about-image img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .about-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 3em;
            margin-bottom: 20px;
            color: #333;
        }
        
        .about-content p {
            font-size: 1.1em;
            line-height: 1.8;
            color: #666;
            margin-bottom: 30px;
        }
        
        /* Restaurants Section */
        .restaurants-section {
            background: #f8f8f8;
            padding: 100px 20px;
        }
        
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }
        
        .section-subtitle {
            font-size: 0.95em;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #667eea;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 3em;
            color: #333;
            margin-bottom: 20px;
        }
        
        .section-description {
            font-size: 1.1em;
            color: #666;
            line-height: 1.6;
        }
        
        .restaurant-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            padding: 0 20px;
        }
        
        .restaurant-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s;
            position: relative;
        }
        
        .restaurant-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .restaurant-image {
            height: 280px;
            overflow: hidden;
            position: relative;
        }
        
        .restaurant-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .restaurant-card:hover .restaurant-image img {
            transform: scale(1.1);
        }
        
        .restaurant-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .restaurant-info {
            padding: 35px;
        }
        
        .restaurant-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.8em;
            margin-bottom: 15px;
            color: #333;
        }
        
        .restaurant-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .restaurant-link {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05em;
            transition: all 0.3s;
        }
        
        .restaurant-link:hover {
            color: #764ba2;
            transform: translateX(5px);
        }
        
        /* Featured Products */
        .featured-section {
            padding: 100px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }
        
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 250px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }

        .out-of-stock-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.55);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .out-of-stock-badge {
            background: #eb3349;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.95em;
            letter-spacing: 1px;
        }
        
        .product-info {
            padding: 30px;
        }
        
        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #333;
        }
        
        .product-restaurant {
            color: #999;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        
        .product-price {
            font-size: 1.6em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
        }
        
        .add-to-cart-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.05em;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .add-to-cart-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* Responsive */
        @media (max-width: 968px) {
            .hero-title {
                font-size: 3.5em;
            }
            
            .about-section {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .restaurant-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                height: 70vh;
                min-height: 500px;
            }
            
            .hero-title {
                font-size: 2.5em;
            }
            
            .hero-description {
                font-size: 1.1em;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                max-width: 300px;
            }
            
            .section-title {
                font-size: 2em;
            }
            
            .about-content h2 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    
    <?php if (!$restaurant): ?>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <div class="hero-subtitle">Welcome to</div>
                <h1 class="hero-title">SmartDine Hub</h1>
                <p class="hero-description">Explore our vast collection of restaurants, all in one place</p>
                <div class="hero-buttons">
                    <a href="#restaurants" class="btn-primary">Browse Now</a>
                    <a href="pages/user/search.php" class="btn-secondary">Browse Menu</a>
                </div>
            </div>
        </section>
        
        <!-- About Section -->
        <section class="about-section">
            <div class="about-image">
                <img src="assets/images/food pics/Grilled Lobster.jpg" alt="Delicious Food">
            </div>
            <div class="about-content">
                <h2>Taste the Difference</h2>
                <p>At SmartDine Hub, we bring together the best restaurants across Uganda's regions under one platform. From traditional Ugandan cuisine to international flavors, discover a world of taste at your fingertips.</p>
                <p>Our carefully curated selection of partner restaurants ensures that every meal is a memorable experience. Fresh ingredients, expert chefs, and fast delivery - that's the SmartDine Hub promise.</p>
                <a href="pages/user/search.php" class="btn-primary">Order Now</a>
            </div>
        </section>
        
        <!-- Restaurants Section -->
        <section class="restaurants-section" id="restaurants">
            <div class="section-header">
                <div class="section-subtitle">Our Partners</div>
                <h2 class="section-title">Explore Uganda</h2>
                <p class="section-description">Your All In One Food Source</p>
            </div>

            <?php foreach ($regionMap as $regionName => $regionRests): ?>
            <div style="max-width:1400px; margin:0 auto 60px; padding:0 20px;">
                <h3 style="font-family:'Playfair Display',serif; font-size:1.8em; color:#333; margin-bottom:30px; padding-left:5px; border-left:4px solid #667eea; padding-left:15px;">
                    <?php echo htmlspecialchars($regionName); ?> Region
                </h3>
                <div class="restaurant-grid">
                <?php foreach ($regionRests as $rest):
                    if (!in_array($rest, $restaurants)) continue;
                    $image = $restaurantImages[$rest] ?? 'Cheeseburger.jpg';
                    $description = $restaurantDescriptions[$rest] ?? 'Delicious food awaits you';
                ?>
                    <div class="restaurant-card">
                        <div class="restaurant-image">
                            <img src="assets/images/food pics/<?php echo $image; ?>" alt="<?php echo htmlspecialchars($rest); ?>">
                            <div class="restaurant-badge"><?php echo htmlspecialchars($regionName); ?></div>
                        </div>
                        <div class="restaurant-info">
                            <h3 class="restaurant-name"><?php echo htmlspecialchars($rest); ?></h3>
                            <p class="restaurant-description"><?php echo $description; ?></p>
                            <a href="pages/restaurant.php?name=<?php echo urlencode($rest); ?>" class="restaurant-link">View Menu →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
        
    <?php else: ?>
        <!-- Restaurant Menu Page -->
        <section class="featured-section">
            <div class="section-header">
                <div class="section-subtitle"><?php echo htmlspecialchars($restaurant); ?></div>
                <h2 class="section-title">Our Menu</h2>
                <p class="section-description">Discover our delicious selection of carefully crafted dishes</p>
            </div>
            
            <div class="product-grid">
                <?php foreach ($results as $product): ?>
                    <?php $inStock = isset($product['in_stock']) ? (int)$product['in_stock'] : 1; ?>
                    <div class="product-card">
                        <div class="product-image" style="position:relative;">
                            <img src="assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php if (!$inStock): ?>
                            <div class="out-of-stock-overlay"><span class="out-of-stock-badge">Out of Stock</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-restaurant"><?php echo htmlspecialchars($product['restaurant']); ?></p>
                            <?php if (!empty($product['description'])): ?>
                            <p style="color:#888; font-size:0.88em; line-height:1.5; margin-bottom:12px;"><?php echo htmlspecialchars($product['description']); ?></p>
                            <?php endif; ?>
                            <div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div>
                            <?php if ($inStock): ?>
                            <button
                                class="add-to-cart-btn"
                                type="button"
                                data-add-to-cart
                                data-product-id="<?php echo (int)$product['id']; ?>"
                                data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-product-price="<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-restaurant="<?php echo htmlspecialchars($product['restaurant'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="ri-shopping-cart-line"></i> Add to Cart
                            </button>
                            <?php else: ?>
                            <button class="add-to-cart-btn" disabled style="opacity:0.5;cursor:not-allowed;">Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    
    <?php include 'includes/footer.php'; ?>
