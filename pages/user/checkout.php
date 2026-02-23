<?php
session_start();
include '../../includes/connect.php';

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
            $allProducts = json_decode(file_get_contents('../../data/products.json'), true);
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
        $allProducts = json_decode(file_get_contents('../../data/products.json'), true);
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
        $priceStr = str_replace(['UGX', ' '], '', $item['price']);
        $price = floatval($priceStr);
        $total += $price;
    }
}

// Process checkout form submission
$orderSuccess = false;
$orderId = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cc_number'])) {
    $customerName = $_POST['name'];
    $customerEmail = $_POST['email'];
    $deliveryAddress = $_POST['address'];
    $deliveryCity = $_POST['city'];
    $deliveryZip = $_POST['zip'];
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Create order
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, total_amount, status, delivery_address, delivery_city, delivery_zip, customer_name, customer_email, payment_method)
            VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, 'credit_card')
        ");
        $stmt->execute([$_SESSION['user_id'], $total, $deliveryAddress, $deliveryCity, $deliveryZip, $customerName, $customerEmail]);
        $orderId = $conn->lastInsertId();
        
        // Add order items
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, restaurant)
            VALUES (?, ?, ?, ?, 1, ?)
        ");
        
        foreach ($cartItems as $item) {
            $stmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['restaurant']]);
        }
        
        // Commit transaction
        $conn->commit();
        
        // Clear the cart
        $_SESSION['cart'] = [];
        $orderSuccess = true;
    } catch (Exception $e) {
        $conn->rollBack();
        $message = 'Error processing order: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Smart Dine</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
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
            padding: 20px;
        }
        
        .checkout-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .page-title {
            text-align: center;
            color: white;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .checkout-form {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .order-summary {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .order-summary h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5em;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #333;
            font-size: 1.05em;
        }
        
        .order-total {
            font-weight: 600;
            font-size: 1.3em;
            border-top: 2px solid #f0f0f0;
            margin-top: 15px;
            padding-top: 15px;
            color: #333;
        }
        
        .checkout-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .order-success {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 20px;
        }
        
        .order-success h2 {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2em;
            margin-bottom: 20px;
        }
        
        .order-success p {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        
        .order-success a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin: 0 10px;
        }
        
        .empty-cart-message {
            text-align: center;
            padding: 60px 40px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .empty-cart-message h2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2em;
            margin-bottom: 15px;
        }
        
        .empty-cart-message p {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.1em;
        }
        
        .empty-cart-message a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            display: none;
            z-index: 1000;
            font-weight: 500;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                padding: 0 15px;
            }
            
            .page-title {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div id="notification"></div>
    
    <main>
        <div class="checkout-container">
            <h1 class="page-title">💳 Checkout</h1>
            
            <?php if ($orderSuccess): ?>
                <div class="order-success">
                    <h2>Order Placed Successfully!</h2>
                    <p>Thank you for your purchase. Your order #<?php echo $orderId; ?> has been placed and will be processed shortly.</p>
                    <p>A confirmation email has been sent to your email address.</p>
                    <p><a href="../orders/orders.php">View My Orders</a> | <a href="../../home.php">Continue Shopping</a></p>
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

    <?php include '../../includes/footer.php'; ?>

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

