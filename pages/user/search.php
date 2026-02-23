<?php
session_start();
// Use the products.json file for products data
$products = json_decode(file_get_contents('../../data/products.json'), true);

// Get search parameters
$query = isset($_GET['query']) ? strtolower($_GET['query']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Filter products based on search criteria
$results = array_filter($products, function ($product) use ($query, $category) {
    $matchesQuery = empty($query) || strpos(strtolower($product['name']), $query) !== false;
    $matchesCategory = empty($category) || $product['category'] === $category;
    return $matchesQuery && $matchesCategory;
});

// Add IDs to products if they don't have them
foreach ($results as $index => $product) {
    if (!isset($product['id'])) {
        $results[$index]['id'] = $index + 1;  // Add sequential ID
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Dishes - Smart Dine</title>
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
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
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
        
        .search-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        
        .search-header h2 {
            font-size: 3em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
        }
        
        #search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }
        
        #search-form input {
            flex: 1;
            min-width: 250px;
            padding: 15px 25px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 30px;
            font-size: 1em;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        
        #search-form input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        #search-form select {
            padding: 15px 25px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 30px;
            font-size: 1em;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 200px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        
        #search-form select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        #search-form button {
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        #search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        }
        
        .results-count {
            text-align: center;
            color: white;
            font-size: 1.3em;
            margin: 30px 0;
            font-weight: 600;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
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
        
        .product-price {
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
        
        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        .no-results h2 {
            color: #667eea;
            font-size: 2em;
            margin-bottom: 15px;
        }
        
        .no-results p {
            color: #333;
            font-size: 1.1em;
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
            
            #search-form {
                flex-direction: column;
            }
            
            #search-form input,
            #search-form select,
            #search-form button {
                width: 100%;
                min-width: auto;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .search-header {
                padding: 30px 20px;
            }
            
            .search-header h2 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    
    <header>
        <div class="header-content">
            <h1>🔍 Search Dishes</h1>
            <div class="user-info">
                <span class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="../auth/logout.php" class="logout-btn">🚪 Logout</a>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="../../home.php">🏠 Home</a></li>
                <li><a href="search.php">🔍 Search</a></li>
                <li><a href="profile.php">👤 Profile</a></li>
                <li><a href="../orders/orders.php">📦 Orders</a></li>
                <li><a href="cart.php">🛒 Cart (<span id="cart-count">0</span>)</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/index.php" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333;">⚙️ Admin</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="search-header">
            <h2>Find Your Favorite Dish</h2>
            <form id="search-form" action="search.php" method="GET">
                <input type="text" 
                       name="query" 
                       placeholder="🔍 Search for dishes..." 
                       value="<?php echo htmlspecialchars($query); ?>">
                <select name="category">
                    <option value="">📋 All Categories</option>
                    <option value="appetizer" <?php echo $category === 'appetizer' ? 'selected' : ''; ?>>🥗 Appetizers</option>
                    <option value="main-course" <?php echo $category === 'main-course' ? 'selected' : ''; ?>>🍽️ Main Courses</option>
                    <option value="dessert" <?php echo $category === 'dessert' ? 'selected' : ''; ?>>🍰 Desserts</option>
                    <option value="beverage" <?php echo $category === 'beverage' ? 'selected' : ''; ?>>🥤 Beverages</option>
                </select>
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if (!empty($query) || !empty($category)): ?>
            <div class="results-count">
                <?php echo count($results); ?> dish<?php echo count($results) !== 1 ? 'es' : ''; ?> found
            </div>
        <?php endif; ?>

        <div class="product-grid">
            <?php if (empty($results)): ?>
                <div class="no-results">
                    <h2>🍽️ No dishes found</h2>
                    <p>Try adjusting your search criteria or browse all categories</p>
                </div>
            <?php else: ?>
                <?php foreach ($results as $product): ?>
                    <div class="product">
                        <img src="../../assets/images/food pics/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-restaurant">
                                🏪 <?php echo htmlspecialchars($product['restaurant']); ?>
                            </div>
                            <div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div>
                            <button onclick="addToCart(<?php echo isset($product['id']) ? $product['id'] : $index + 1; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo htmlspecialchars($product['price']); ?>', '<?php echo addslashes($product['restaurant']); ?>')">
                                🛒 Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>📞 Contact us: +123-456-7890 | Smart Dine - Taste the Difference 🍽️</p>
    </footer>

    <script>
    // Function to show notification
    function showNotification(message, duration = 3000) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, duration);
    }

    // Function to add a product to the cart
    function addToCart(productId, productName, productPrice, restaurant = 'Smart Dine') {
        showNotification('Adding to cart...');
        
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../../api/add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            showNotification(`✅ ${productName} added to cart!`);
                            updateCartCount();
                        } else {
                            showNotification(response.message || "Failed to add product to cart.");
                        }
                    } catch (e) {
                        showNotification("Error processing server response.");
                        console.error("Error parsing JSON:", e, xhr.responseText);
                    }
                } else {
                    showNotification("Server error. Please try again later.");
                }
            }
        };

        xhr.send(`product_id=${productId}&restaurant=${encodeURIComponent(restaurant)}`);
    }

    // Function to update the cart count displayed on the page
    function updateCartCount() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "../../api/get_cart_count.php", true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const cartCountElement = document.getElementById("cart-count");
                        if (cartCountElement) {
                            cartCountElement.textContent = response.count;
                        }
                    }
                } catch (e) {
                    console.error("Error parsing JSON:", e, xhr.responseText);
                }
            }
        };

        xhr.send();
    }

    // Update cart count when page loads
    document.addEventListener("DOMContentLoaded", function() {
        updateCartCount();
    });
    </script>
</body>
</html>

