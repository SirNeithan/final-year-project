<?php
session_start();
include 'includes/connect.php'; // Using the fixed config file

// Initialize cart items array
$cartItems = [];

// Check if we have products in the cart
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $allProducts = json_decode(file_get_contents('data/products.json'), true);
    
    // Add IDs to products if they don't have them
    foreach ($allProducts as $index => $product) {
        if (!isset($product['id'])) {
            $allProducts[$index]['id'] = $index + 1;
        }
    }
    
    // Filter products that are in the cart
    $cartItems = [];
    foreach ($_SESSION['cart'] as $cartItem) {
        foreach ($allProducts as $product) {
            if ($product['id'] == $cartItem['id'] && $product['restaurant'] == $cartItem['restaurant']) {
                $cartItems[] = $product;
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Nate Liquor Stores</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Cart specific styles */
        .cart-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
        }
        .cart-item-info {
            flex-grow: 1;
        }
        .cart-item button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .checkout-btn {
            display: inline-block;
            background-color: #333;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .empty-cart {
            text-align: center;
            padding: 30px;
            color: #777;
        }
        .cart-summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .cart-summary p {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .cart-summary .total {
            font-weight: bold;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
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
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .cart-item img {
                margin-bottom: 10px;
            }
            .cart-item button {
                margin-top: 10px;
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    
    <header>
        <h1>Your Cart</h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="search.php">Search Products</a></li>
                <li><a href="cart.php">Cart (<span id="cart-count"><?php echo count($cartItems); ?></span>)</a></li>
                <li><a href="checkout.php">Checkout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="cart-container">
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <p>Go back to <a href="search.php">Search Products</a> to add items to your cart.</p>
                </div>
            <?php else: ?>
                <div id="cart-items">
                    <?php 
                    $total = 0;
                    foreach ($cartItems as $item): 
                        // Extract price as a number
                        $priceStr = str_replace('KSh ', '', $item['price']);
                        $price = floatval($priceStr);
                        $total += $price;
                    ?>
                        <div class="cart-item">
                            <img src="assets/images/food pics/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22286%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20286%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_17e19a89e35%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3A-apple-system%2CBlinkMacSystemFont%2C%26quot%3BSegoe%20UI%26quot%3B%2CRoboto%2C%26quot%3BHelvetica%20Neue%26quot%3B%2CArial%2C%26quot%3BNoto%20Sans%26quot%3B%2Csans-serif%2C%26quot%3BApple%20Color%20Emoji%26quot%3B%2C%26quot%3BSegoe%20UI%20Emoji%26quot%3B%2C%26quot%3BSegoe%20UI%20Symbol%26quot%3B%2C%26quot%3BNoto%20Color%20Emoji%26quot%3B%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_17e19a89e35%22%3E%3Crect%20width%3D%22286%22%20height%3D%22180%22%20fill%3D%22%23373940%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22108.5390625%22%20y%3D%2297.5%22%3E<?php echo $item['name']; ?>%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E'">
                            <div class="cart-item-info">
                                <h3><?php echo $item['name']; ?></h3>
                                <p><?php echo $item['price']; ?></p>
                                <p><small>From: <?php echo $item['restaurant']; ?></small></p>
                            </div>
                            <button onclick="removeFromCart(<?php echo $item['id']; ?>, '<?php echo addslashes($item['restaurant']); ?>')">Remove</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <p><span>Subtotal:</span> <span>KSh <?php echo number_format($total, 2); ?></span></p>
                    <p><span>Shipping:</span> <span>Free</span></p>
                    <p class="total"><span>Total:</span> <span>KSh <?php echo number_format($total, 2); ?></span></p>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
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

    // Function to remove a product from the cart
    function removeFromCart(productId, restaurant) {
        // Show loading indicator
        showNotification('Removing from cart...');
        
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "api/remove_from_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            showNotification("Product removed from cart.");
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            showNotification(response.message || "Failed to remove product from cart.");
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
    </script>
</body>
</html>

