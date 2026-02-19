<?php
session_start();
include 'includes/connect.php';

// Initialize cart items array
$cartItems = [];
$total = 0;

// Check if we have products in the cart
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartIds = array_column($_SESSION['cart'], 'id');
    // Try to get products from database first
    if (isset($conn)) {
        try {
            $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
            $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute($cartIds);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback to JSON file
            $allProducts = json_decode(file_get_contents('data/products.json'), true);
            foreach ($allProducts as $index => $product) {
                if (!isset($product['id'])) {
                    $allProducts[$index]['id'] = $index + 1;
                }
            }
            $cartItems = array_filter($allProducts, function($product) use ($cartIds) {
                return in_array($product['id'], $cartIds);
            });
        }
    } else {
        // Fallback to JSON file
        $allProducts = json_decode(file_get_contents('data/products.json'), true);
        foreach ($allProducts as $index => $product) {
            if (!isset($product['id'])) {
                $allProducts[$index]['id'] = $index + 1;
            }
        }
        $cartItems = array_filter($allProducts, function($product) use ($cartIds) {
            return in_array($product['id'], $cartIds);
        });
    }

    // Calculate total
    foreach ($cartItems as $item) {
        $priceStr = str_replace('$', '', $item['price']);
        $price = floatval($priceStr);
        $total += $price;
    }
}

// Process checkout form submission
$orderSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cc_number'])) {
    // In a real app, you would process payment here
    // For this demo, we'll just simulate a successful order
    $orderSuccess = true;
    
    // Clear the cart after successful order
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Nate Liquor Stores</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .checkout-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .order-summary {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .order-summary h3 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .order-total {
            font-weight: bold;
            border-top: 1px solid #ddd;
            margin-top: 15px;
            padding-top: 10px;
        }
        .checkout-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        .checkout-btn:hover {
            background-color: #45a049;
        }
        .order-success {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
        }
        .order-success h2 {
            margin-bottom: 15px;
        }
        .empty-cart-message {
            text-align: center;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .checkout-container {
                padding: 0 15px;
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
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    
    <header>
        <h1>Checkout</h1>
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
        <div class="checkout-container">
            <?php if ($orderSuccess): ?>
                <div class="order-success">
                    <h2>Order Placed Successfully!</h2>
                    <p>Thank you for your purchase. Your order has been placed and will be processed shortly.</p>
                    <p>A confirmation email has been sent to your email address.</p>
                    <p><a href="index.html">Continue Shopping</a></p>
                </div>
            <?php elseif (empty($cartItems)): ?>
                <div class="empty-cart-message">
                    <h2>Your cart is empty</h2>
                    <p>You need to add products to your cart before checking out.</p>
                    <p><a href="search.php">Browse Products</a></p>
                </div>
            <?php else: ?>
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="order-item">
                            <span><?php echo $item['name']; ?></span>
                            <span><?php echo $item['price']; ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="order-item order-total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>

                <form class="checkout-form" method="post" action="checkout.php">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="zip">ZIP Code</label>
                        <input type="text" id="zip" name="zip" required>
                    </div>
                    <div class="form-group">
                        <label for="cc_number">Credit Card Number</label>
                        <input type="text" id="cc_number" name="cc_number" placeholder="**** **** **** ****" required>
                    </div>
                    <div class="form-group" style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label for="cc_exp">Expiry Date</label>
                            <input type="text" id="cc_exp" name="cc_exp" placeholder="MM/YY" required>
                        </div>
                        <div style="flex: 1;">
                            <label for="cc_cvv">CVV</label>
                            <input type="text" id="cc_cvv" name="cc_cvv" placeholder="123" required>
                        </div>
                    </div>
                    <button type="submit" class="checkout-btn">Pay Now ($<?php echo number_format($total, 2); ?>)</button>
                </form>

                <p style="text-align: center; margin-top: 20px;">Or contact us at 0766191751 to place your order.</p>
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

