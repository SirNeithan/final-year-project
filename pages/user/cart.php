<?php
session_start();
include '../../includes/connect.php'; // Using the fixed config file

// Initialize cart items array
$cartItems = [];

// Check if we have products in the cart
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $allProducts = json_decode(file_get_contents('../../data/products.json'), true);
    
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
    <title>Cart - Smart Dine</title>
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
        
        .cart-container {
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
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .cart-item:hover {
            transform: translateY(-3px);
        }
        
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 15px;
            margin-right: 20px;
        }
        
        .cart-item-info {
            flex-grow: 1;
        }
        
        .cart-item-info h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.3em;
            margin-bottom: 5px;
        }
        
        .cart-item-info p {
            color: #333;
            margin: 5px 0;
        }
        
        .cart-item button {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .cart-item button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(235, 51, 73, 0.4);
        }
        
        .checkout-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 40px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .empty-cart h2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2em;
            margin-bottom: 15px;
        }
        
        .empty-cart p {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.1em;
        }
        
        .empty-cart a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .cart-summary {
            margin-top: 30px;
            padding: 25px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .cart-summary h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        
        .cart-summary p {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            color: #333;
            font-size: 1.05em;
        }
        
        .cart-summary .total {
            font-weight: 600;
            font-size: 1.3em;
            border-top: 2px solid #f0f0f0;
            padding-top: 15px;
            margin-top: 15px;
            color: #333;
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
            animation: fadeIn 0.3s, fadeOut 0.3s 2.7s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-weight: 500;
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
                margin-bottom: 15px;
            }
            
            .cart-item button {
                margin-top: 15px;
                align-self: flex-end;
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
        <div class="cart-container">
            <h1 class="page-title">🛒 Your Cart</h1>
            
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
                            <img src="../../assets/images/food pics/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22286%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20286%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_17e19a89e35%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3A-apple-system%2CBlinkMacSystemFont%2C%26quot%3BSegoe%20UI%26quot%3B%2CRoboto%2C%26quot%3BHelvetica%20Neue%26quot%3B%2CArial%2C%26quot%3BNoto%20Sans%26quot%3B%2Csans-serif%2C%26quot%3BApple%20Color%20Emoji%26quot%3B%2C%26quot%3BSegoe%20UI%20Emoji%26quot%3B%2C%26quot%3BSegoe%20UI%20Symbol%26quot%3B%2C%26quot%3BNoto%20Color%20Emoji%26quot%3B%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_17e19a89e35%22%3E%3Crect%20width%3D%22286%22%20height%3D%22180%22%20fill%3D%22%23373940%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22108.5390625%22%20y%3D%2297.5%22%3E<?php echo $item['name']; ?>%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E'">
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

    // Function to remove a product from the cart
    function removeFromCart(productId, restaurant) {
        // Show loading indicator
        showNotification('Removing from cart...');
        
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../../api/remove_from_cart.php", true);
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
    </script>
</body>
</html>

