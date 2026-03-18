<?php
session_start();
include '../../includes/connect.php';

$cartItems = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $allProducts = json_decode(file_get_contents('../../data/products.json'), true);
    foreach ($allProducts as $index => $product) {
        if (!isset($product['id'])) $allProducts[$index]['id'] = $index + 1;
    }

    foreach ($_SESSION['cart'] as $cartItem) {
        foreach ($allProducts as $product) {
            if ($product['id'] == $cartItem['id'] && $product['restaurant'] == $cartItem['restaurant']) {
                $product['quantity'] = $cartItem['quantity'] ?? 1;
                $cartItems[] = $product;
                break;
            }
        }
    }
}

foreach ($cartItems as $item) {
    $priceStr = str_replace(['UGX', 'KSh', ' ', ','], '', $item['price']);
    $total += floatval($priceStr) * ($item['quantity'] ?? 1);
}

$pageTitle = "Shopping Cart - Smart Dine";
$headerTitle = "Smart Dine";
include '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/elegant-theme.css">

<style>
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.cart-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 40px;
    margin-top: 40px;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cart-item {
    background: white;
    border-radius: 20px;
    padding: 25px;
    display: flex;
    gap: 25px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s;
}

.cart-item:hover {
    box-shadow: 0 8px 35px rgba(0,0,0,0.12);
}

.cart-item-image {
    width: 140px;
    height: 140px;
    border-radius: 15px;
    overflow: hidden;
    flex-shrink: 0;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.cart-item-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.5em;
    color: #333;
    margin-bottom: 8px;
}

.cart-item-restaurant {
    color: #999;
    font-size: 0.95em;
    margin-bottom: 15px;
}

.cart-item-price {
    font-size: 1.6em;
    font-weight: 700;
    color: #667eea;
}

.cart-item-remove {
    align-self: flex-start;
    background: #fee;
    color: #c33;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.cart-item-remove:hover {
    background: #c33;
    color: white;
}

.cart-summary {
    background: white;
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    position: sticky;
    top: 100px;
    height: fit-content;
}

.summary-title {
    font-family: 'Playfair Display', serif;
    font-size: 2em;
    margin-bottom: 25px;
    color: #333;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 1.1em;
}

.summary-row.total {
    border-bottom: none;
    border-top: 2px solid #667eea;
    margin-top: 15px;
    padding-top: 20px;
    font-size: 1.4em;
    font-weight: 700;
    color: #667eea;
}

.checkout-btn {
    width: 100%;
    margin-top: 25px;
}

@media (max-width: 968px) {
    .cart-grid {
        grid-template-columns: 1fr;
    }
    
    .cart-summary {
        position: static;
    }
    
    .cart-item {
        flex-direction: column;
    }
    
    .cart-item-image {
        width: 100%;
        height: 200px;
    }
}
</style>

<div class="cart-container">
    <div class="page-header">
        <div class="page-subtitle">Your Order</div>
        <h1 class="page-title">Shopping Cart</h1>
        <p class="page-description">
            Review your items before proceeding to checkout
        </p>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="ri-shopping-cart-line" style="font-size:3rem;color:#ccc;"></i></div>
            <h2 class="empty-state-title">Your Cart is Empty</h2>
            <p class="empty-state-description">
                Looks like you haven't added any items to your cart yet. Start exploring our delicious menu!
            </p>
            <a href="../user/search.php" class="btn btn-primary">Browse Menu</a>
        </div>
    <?php else: ?>
        <div class="cart-grid">
            <div class="cart-items">
                <?php foreach ($cartItems as $item): 
                    $qty = $item['quantity'] ?? 1;
                    $unitPrice = floatval(str_replace(['UGX', 'KSh', ' ', ','], '', $item['price']));
                    $lineTotal = $unitPrice * $qty;
                ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="../../assets/images/food pics/<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="cart-item-details">
                            <div>
                                <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="cart-item-restaurant">from <?php echo htmlspecialchars($item['restaurant']); ?></p>
                                <?php if (!empty($item['description'])): ?>
                                <p style="color:#aaa; font-size:0.88em; margin-top:4px;"><?php echo htmlspecialchars($item['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap; margin-top:15px;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <button onclick="changeQty(<?php echo $item['id']; ?>, '<?php echo addslashes($item['restaurant']); ?>', <?php echo $qty - 1; ?>)" 
                                            style="width:32px;height:32px;border-radius:50%;border:2px solid #667eea;background:white;color:#667eea;font-size:1.2em;cursor:pointer;font-weight:700;">−</button>
                                    <span style="font-weight:700;font-size:1.1em;min-width:20px;text-align:center;"><?php echo $qty; ?></span>
                                    <button onclick="changeQty(<?php echo $item['id']; ?>, '<?php echo addslashes($item['restaurant']); ?>', <?php echo $qty + 1; ?>)"
                                            style="width:32px;height:32px;border-radius:50%;border:none;background:linear-gradient(135deg,#667eea,#764ba2);color:white;font-size:1.2em;cursor:pointer;font-weight:700;">+</button>
                                </div>
                                <div class="cart-item-price">UGX <?php echo number_format($lineTotal, 0); ?></div>
                            </div>
                        </div>
                        <button class="cart-item-remove" 
                                onclick="removeFromCart(<?php echo $item['id']; ?>, '<?php echo addslashes($item['restaurant']); ?>')">
                            Remove
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2 class="summary-title">Order Summary</h2>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>UGX <?php echo number_format($total, 0); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery</span>
                    <span>Free</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>UGX <?php echo number_format($total, 0); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary checkout-btn">
                    Proceed to Checkout
                </a>
                <a href="../user/search.php" class="btn btn-secondary btn-full" style="margin-top: 15px;">
                    Continue Shopping
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>

<script src="../../assets/js/script.js"></script>
<script>
function removeFromCart(productId, restaurant) {
    if (!confirm('Remove this item from your cart?')) return;
    
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/remove_from_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || "Failed to remove item");
            }
        }
    };

    xhr.send(`product_id=${productId}&restaurant=${encodeURIComponent(restaurant)}`);
}

function changeQty(productId, restaurant, newQty) {
    if (newQty < 1) {
        removeFromCart(productId, restaurant);
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/update_cart_quantity.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) location.reload();
        }
    };
    xhr.send(`product_id=${productId}&restaurant=${encodeURIComponent(restaurant)}&quantity=${newQty}`);
}
</script>
