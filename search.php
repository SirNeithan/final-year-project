<?php
session_start();
// Use the products.json file for products data
$products = json_decode(file_get_contents('data/products.json'), true);

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
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Same CSS from index.html can be included here */
        .product-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product img {
            max-width: 100%;
            height: auto;
            max-height: 150px;
            object-fit: contain;
        }
        .product button {
            background-color: #333;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .product button:hover {
            background-color: #555;
        }
        #search-form {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        #search-form input, 
        #search-form select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        #search-form button {
            background-color: #333;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .product-grid {
                flex-direction: column;
            }
            #search-form {
                flex-direction: column;
            }
            #search-form input, 
            #search-form select,
            #search-form button {
                width: 100%;
            }
        }
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #333;
            color: white;
            padding: 15px 25px;
            border-radius: 4px;
            display: none;
            z-index: 1000;
            animation: fadeIn 0.3s, fadeOut 0.3s 2.7s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    
    <header>
        <h1>Search Dishes</h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="search.php">Search Dishes</a></li>
                <li><a href="cart.php">Cart (<span id="cart-count">0</span>)</a></li>
                <li><a href="checkout.php">Checkout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <form id="search-form" action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search for dishes..." value="<?php echo htmlspecialchars($query); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="appetizer" <?php echo $category === 'appetizer' ? 'selected' : ''; ?>>Appetizers</option>
                <option value="main-course" <?php echo $category === 'main-course' ? 'selected' : ''; ?>>Main Courses</option>
                <option value="dessert" <?php echo $category === 'dessert' ? 'selected' : ''; ?>>Desserts</option>
                <option value="beverage" <?php echo $category === 'beverage' ? 'selected' : ''; ?>>Beverages</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <div id="search-results" class="product-grid">
            <?php if (empty($results)): ?>
                <p class="no-results">No products found matching your search criteria.</p>
            <?php else: ?>
                <?php foreach ($results as $product): ?>
                    <div class="product">
                        <img src="assets/images/food pics/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22286%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20286%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_17e19a89e35%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3A-apple-system%2CBlinkMacSystemFont%2C%26quot%3BSegoe%20UI%26quot%3B%2CRoboto%2C%26quot%3BHelvetica%20Neue%26quot%3B%2CArial%2C%26quot%3BNoto%20Sans%26quot%3B%2Csans-serif%2C%26quot%3BApple%20Color%20Emoji%26quot%3B%2C%26quot%3BSegoe%20UI%20Emoji%26quot%3B%2C%26quot%3BSegoe%20UI%20Symbol%26quot%3B%2C%26quot%3BNoto%20Color%20Emoji%26quot%3B%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_17e19a89e35%22%3E%3Crect%20width%3D%22286%22%20height%3D%22180%22%20fill%3D%22%23373940%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22108.5390625%22%20y%3D%2297.5%22%3E<?php echo $product['name']; ?>%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E'">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['price']; ?></p>
                        <button onclick="addToCart(<?php echo isset($product['id']) ? $product['id'] : $index + 1; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>')">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>Contact us: 0766191751</p>
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
    function addToCart(productId, productName, productPrice) {
        // Show loading indicator
        showNotification('Adding to cart...');
        
        // Send an AJAX request to add the product to the cart
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "api/add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            showNotification(`${productName} added to cart!`);
                            updateCartCount(); // Update the cart count displayed on the page
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

        // Send the product ID to the server
        xhr.send(`product_id=${productId}`);
    }

    // Function to update the cart count displayed on the page
    function updateCartCount() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "api/get_cart_count.php", true);

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

