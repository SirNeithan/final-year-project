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
        // Remove currency symbols and commas from price
        $priceStr = str_replace(['UGX', 'KSh', ' ', ','], '', $item['price']);
        $price = floatval($priceStr);
        $total += $price;
    }
}

// Check for success from URL parameters
$orderSuccess = isset($_GET['success']) && $_GET['success'] == '1';
$orderId = $_GET['order_id'] ?? null;
$transactionId = $_GET['transaction_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Smart Dine</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/elegant-theme.css">
    <style>
        body {
            background: #ffffff;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 40px;
            margin-top: 40px;
        }
        
        .checkout-form-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8em;
            margin-bottom: 25px;
            color: #333;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .order-summary-section {
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        
        .order-items {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }
        
        .order-item-details {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-item-name {
            font-weight: 600;
            color: #333;
        }
        
        .order-item-price {
            color: #667eea;
            font-weight: 600;
        }
        
        .order-total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(102, 126, 234, 0.3);
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 1.1em;
        }
        
        .total-row.final {
            border-top: 2px solid rgba(255,255,255,0.3);
            margin-top: 15px;
            padding-top: 20px;
            font-size: 1.6em;
            font-weight: 700;
        }
        
        .pay-btn {
            width: 100%;
            padding: 20px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2em;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s;
        }
        
        .pay-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 20px rgba(255,255,255,0.3);
        }
        
        .pay-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
            display: none;
        }
        
        .success-page {
            max-width: 700px;
            margin: 60px auto;
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .success-icon {
            font-size: 5em;
            margin-bottom: 25px;
        }
        
        .success-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5em;
            color: #333;
            margin-bottom: 20px;
        }
        
        .success-details {
            background: #f8f8f8;
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
            text-align: left;
        }
        
        .success-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .success-detail-row:last-child {
            border-bottom: none;
        }
        
        .success-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        @media (max-width: 968px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            
            .order-summary-section {
                position: static;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
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
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div id="notification"></div>
    
    <main>
        <div class="checkout-container">
            <div class="page-header">
                <div class="page-subtitle">Secure Payment</div>
                <h1 class="page-title">Checkout</h1>
                <p class="page-description">Complete your order with our secure payment system</p>
            </div>
            
            <?php if ($orderSuccess): ?>
                <div class="success-page">
                    <div class="success-icon">✅</div>
                    <h2 class="success-title">Order Placed Successfully!</h2>
                    <p style="font-size: 1.2em; color: #666; margin: 20px 0;">Thank you for your order!</p>
                    
                    <div class="success-details">
                        <div class="success-detail-row">
                            <strong>Order Number:</strong>
                            <span>#<?php echo htmlspecialchars($orderId); ?></span>
                        </div>
                        <div class="success-detail-row">
                            <strong>Transaction ID:</strong>
                            <span><?php echo htmlspecialchars($transactionId); ?></span>
                        </div>
                        <div class="success-detail-row">
                            <strong>Total Amount:</strong>
                            <span>UGX <?php echo number_format($total, 0); ?></span>
                        </div>
                    </div>
                    
                    <div style="background: #e8f5e9; padding: 20px; border-radius: 15px; margin: 25px 0; border-left: 4px solid #4caf50; text-align: left;">
                        <p style="margin: 8px 0; color: #2e7d32; line-height: 1.8;">
                            📧 A confirmation email has been sent to your email address.<br>
                            📱 We'll contact you shortly on your phone number for delivery coordination.<br>
                            🚚 Expected delivery: 30-45 minutes
                        </p>
                    </div>
                    
                    <div class="success-actions">
                        <a href="../orders/orders.php" class="btn btn-primary">📦 View My Orders</a>
                        <a href="../../home.php" class="btn btn-secondary">🏠 Continue Shopping</a>
                    </div>
                </div>
            <?php elseif (empty($cartItems)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🛒</div>
                    <h2 class="empty-state-title">Your cart is empty</h2>
                    <p class="empty-state-description">You need to add products to your cart before checking out.</p>
                    <a href="search.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="checkout-grid">
                    <div class="checkout-form-section">
                        <h2 class="section-title">Billing Information</h2>
                        
                        <form id="checkout-form">
                            <div id="error-message" class="error-message"></div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Delivery Address</label>
                                <input type="text" id="address" name="address" class="form-control" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" name="city" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="0700123456" required>
                                    <small style="color: #999; font-size: 0.85em;">For delivery coordination</small>
                                </div>
                            </div>
                            
                            <h2 class="section-title" style="margin-top: 40px;">Payment Details</h2>
                            
                            <div class="form-group">
                                <label for="cc_number">Credit Card Number</label>
                                <input type="text" id="cc_number" name="cc_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required>
                                <small style="color: #999; font-size: 0.85em;">Test card: 4532015112830366</small>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="cc_exp">Expiry Date</label>
                                    <input type="text" id="cc_exp" name="cc_exp" class="form-control" placeholder="MM/YY" maxlength="5" required>
                                    <small style="color: #999; font-size: 0.85em;">e.g., 12/25</small>
                                </div>
                                <div class="form-group">
                                    <label for="cc_cvv">CVV</label>
                                    <input type="text" id="cc_cvv" name="cc_cvv" class="form-control" placeholder="123" maxlength="4" required>
                                    <small style="color: #999; font-size: 0.85em;">3-4 digits</small>
                                </div>
                            </div>
                            
                            <p style="text-align: center; margin: 25px 0; color: #999; font-size: 0.9em;">
                                🔒 This is a simulated payment system for educational purposes
                            </p>
                        </form>
                    </div>
                    
                    <div class="order-summary-section">
                        <div class="order-items">
                            <h3 class="section-title">Order Summary</h3>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="order-item">
                                    <img src="../../assets/images/food pics/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="order-item-image">
                                    <div class="order-item-details">
                                        <span class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                        <span class="order-item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-total-section">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>UGX <?php echo number_format($total, 0); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Delivery:</span>
                                <span>Free</span>
                            </div>
                            <div class="total-row final">
                                <span>Total:</span>
                                <span>UGX <?php echo number_format($total, 0); ?></span>
                            </div>
                            
                            <button type="submit" form="checkout-form" class="pay-btn" id="pay-btn">
                                <span id="btn-text">Pay Now</span>
                                <span id="btn-loader" style="display: none;">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
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

    // Format card number with spaces
    document.getElementById('cc_number')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Format expiry date
    document.getElementById('cc_exp')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        e.target.value = value;
    });

    // Only allow numbers in CVV
    document.getElementById('cc_cvv')?.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    // Handle form submission
    document.getElementById('checkout-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const payBtn = document.getElementById('pay-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoader = document.getElementById('btn-loader');
        const errorMessage = document.getElementById('error-message');
        
        // Disable button and show loader
        payBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';
        errorMessage.style.display = 'none';
        
        // Get form data
        const formData = {
            card_number: document.getElementById('cc_number').value,
            expiry: document.getElementById('cc_exp').value,
            cvv: document.getElementById('cc_cvv').value,
            amount: <?php echo $total; ?>,
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            address: document.getElementById('address').value,
            city: document.getElementById('city').value,
            phone: document.getElementById('phone').value
        };
        
        try {
            // Process payment
            const paymentResponse = await fetch('../../api/process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const paymentResult = await paymentResponse.json();
            
            if (paymentResult.success) {
                // Payment successful, create order
                const orderResponse = await fetch('../../api/create_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ...formData,
                        transaction_id: paymentResult.transaction_id
                    })
                });
                
                const orderResult = await orderResponse.json();
                
                if (orderResult.success) {
                    // Redirect to success page
                    window.location.href = 'checkout.php?success=1&order_id=' + orderResult.order_id + '&transaction_id=' + paymentResult.transaction_id;
                } else {
                    throw new Error(orderResult.message || 'Failed to create order');
                }
            } else {
                throw new Error(paymentResult.message || 'Payment failed');
            }
        } catch (error) {
            // Show error message
            errorMessage.textContent = error.message;
            errorMessage.style.display = 'block';
            
            // Re-enable button
            payBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
        }
    });

    // Update cart count when page loads
    document.addEventListener("DOMContentLoaded", function() {
        updateCartCount();
    });
    </script>
</body>
</html>

