<?php
session_start();
require 'includes/connect.php';

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

if (empty($products)) {
    die('No products found in JSON file.');
}

foreach ($products as $index => $product) {
    if (!isset($product['id'])) {
        $products[$index]['id'] = $index + 1;
    }
}

if ($restaurant) {
    $results = array_filter($products, function ($product) use ($restaurant) {
        return $product['restaurant'] === $restaurant;
    });
    $pageTitle = "$restaurant - Smart Dine";
    $headerTitle = "🍽️ " . htmlspecialchars($restaurant);
} else {
    $restaurants = array_unique(array_column($products, 'restaurant'));
    $pageTitle = "Smart Dine - Multi-Restaurant Platform";
    $headerTitle = "🍽️ Smart Dine";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        header h1 {
            font-size: 2em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95em;
        }
        
        .admin-badge {
            background: #ffd700;
            color: #333;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75em;
            margin-left: 8px;
            font-weight: 700;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }
        
        nav {
            width: 100%;
            margin-top: 15px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        
        nav ul li a {
            color: #667eea;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 20px;
            transition: all 0.3s;
            font-weight: 500;
            background: rgba(102, 126, 234, 0.1);
        }
        
        nav ul li a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
        }
        
        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
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
            margin-bottom: 30px;
        }
        
        .restaurant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .restaurant {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .restaurant::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .restaurant:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        .restaurant-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        
        .restaurant h3 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .restaurant p {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.05em;
        }
        
        .view-menu-btn {
            display: inline-block;
            padding: 15px 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .view-menu-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
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
        
        footer {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            color: #333;
            text-align: center;
            padding: 30px;
            margin-top: 50px;
            font-weight: 500;
        }
        
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            display: none;
            z-index: 2000;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .user-info {
                flex-direction: column;
                width: 100%;
            }
            
            .hero-section {
                padding: 30px 20px;
            }
            
            .hero-section h2 {
                font-size: 2em;
            }
            
            .restaurant-grid, .product-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    
    <header>
        <div class="header-content">
            <h1><?php echo $headerTitle; ?></h1>
            <div class="user-info">
                <span class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <span class="admin-badge">ADMIN</span>
                    <?php endif; ?>
                </span>
                <a href="logout.php" class="logout-btn">🚪 Logout</a>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="home.php">🏠 Home</a></li>
                <?php if ($restaurant): ?>
                    <li><a href="appetizers.php?restaurant=<?php echo urlencode($restaurant); ?>">🥗 Appetizers</a></li>
                    <li><a href="main-courses.php?restaurant=<?php echo urlencode($restaurant); ?>">🍽️ Main Courses</a></li>
                    <li><a href="desserts.php?restaurant=<?php echo urlencode($restaurant); ?>">🍰 Desserts</a></li>
                    <li><a href="beverages.php?restaurant=<?php echo urlencode($restaurant); ?>">🥤 Beverages</a></li>
                <?php endif; ?>
                <li><a href="search.php">🔍 Search</a></li>
                <li><a href="profile.php">👤 Profile</a></li>
                <li><a href="orders.php">📦 Orders</a></li>
                <li><a href="cart.php">🛒 Cart (<span id="cart-count">0</span>)</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/index.php" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333;">⚙️ Admin</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php if ($restaurant): ?>
            <div class="hero-section">
                <h2><?php echo htmlspecialchars($restaurant); ?> Menu</h2>
                <p>Discover our delicious selection of dishes</p>
            </div>
            
            <div class="product-grid">
                <?php foreach ($results as $product): ?>
                    <div class="product">
                        <img src="assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['price']); ?></p>
                            <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo htmlspecialchars($product['price']); ?>', '<?php echo addslashes($product['restaurant']); ?>')">
                                🛒 Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="hero-section">
                <h2>Choose Your Restaurant</h2>
                <p>Explore our amazing selection of partner restaurants</p>
            </div>
            
            <div class="restaurant-grid">
                <?php 
                $icons = ['🍕', '🍝', '🍜', '🌮', '🦞', '🍔', '🍰', '🥗'];
                $iconIndex = 0;
                foreach ($restaurants as $rest): 
                ?>
                    <div class="restaurant">
                        <div class="restaurant-icon"><?php echo $icons[$iconIndex % count($icons)]; ?></div>
                        <h3><?php echo htmlspecialchars($rest); ?></h3>
                        <p>Explore delicious dishes from <?php echo htmlspecialchars($rest); ?></p>
                        <a href="home.php?restaurant=<?php echo urlencode($rest); ?>" class="view-menu-btn">
                            View Menu →
                        </a>
                    </div>
                <?php 
                    $iconIndex++;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>📞 Contact us: +123-456-7890 | Smart Dine - Taste the Difference 🍽️</p>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
