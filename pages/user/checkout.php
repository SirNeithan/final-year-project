<?php
session_start();
include '../../includes/connect.php';

// Load delivery zones
$deliveryZones = [];
try {
    $stmt = $conn->query("SELECT * FROM delivery_zones ORDER BY region, zone_name");
    $deliveryZones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $deliveryZones = [
        ['id'=>1,'zone_name'=>'Kampala Central','region'=>'Central','fee'=>3000],
        ['id'=>2,'zone_name'=>'Kampala Suburbs','region'=>'Central','fee'=>5000],
        ['id'=>3,'zone_name'=>'Jinja',           'region'=>'Eastern','fee'=>7000],
        ['id'=>4,'zone_name'=>'Mbale',            'region'=>'Eastern','fee'=>8000],
        ['id'=>5,'zone_name'=>'Mbarara',          'region'=>'Western','fee'=>9000],
        ['id'=>6,'zone_name'=>'Fort Portal',      'region'=>'Western','fee'=>10000],
        ['id'=>7,'zone_name'=>'Gulu',             'region'=>'Northern','fee'=>12000],
    ];
}

$selectedZoneId = intval($_POST['zone_id'] ?? $_GET['zone_id'] ?? 1);
$deliveryFee = 0;
foreach ($deliveryZones as $z) {
    if ($z['id'] == $selectedZoneId) { $deliveryFee = $z['fee']; break; }
}
if (!$deliveryFee && $deliveryZones) $deliveryFee = $deliveryZones[0]['fee'];

// Load cart items from JSON
$cartItems = [];
$subtotal  = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $allProducts = json_decode(file_get_contents('../../data/products.json'), true);
    foreach ($allProducts as $i => $p) {
        if (!isset($p['id'])) $allProducts[$i]['id'] = $i + 1;
    }
    foreach ($_SESSION['cart'] as $cartItem) {
        foreach ($allProducts as $p) {
            if ($p['id'] == $cartItem['id'] && $p['restaurant'] == $cartItem['restaurant']) {
                $p['quantity'] = $cartItem['quantity'] ?? 1;
                $cartItems[] = $p;
                break;
            }
        }
    }
    foreach ($cartItems as $item) {
        $price     = floatval(str_replace(['UGX','KSh',' ',','], '', $item['price']));
        $subtotal += $price * ($item['quantity'] ?? 1);
    }
}
$total = $subtotal + $deliveryFee;

$orderSuccess = isset($_GET['success']) && $_GET['success'] == '1';
$orderId      = $_GET['order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SmartDine Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/elegant-theme.css">
    <style>
        body { background: #fff; }

        .checkout-container { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 40px;
            margin-top: 40px;
        }

        .checkout-form-section {
            background: white; border-radius: 20px; padding: 40px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08); border: 1px solid #f0f0f0;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6em; margin-bottom: 25px; color: #333;
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        .pod-badge {
            display: flex; align-items: center; gap: 15px;
            background: linear-gradient(135deg, rgba(102,126,234,0.08), rgba(118,75,162,0.08));
            border: 2px solid #667eea; border-radius: 16px;
            padding: 20px 25px; margin-top: 30px;
        }
        .pod-badge .pod-icon { font-size: 2.5em; }
        .pod-badge .pod-text strong { display: block; font-size: 1.1em; color: #333; margin-bottom: 4px; }
        .pod-badge .pod-text span  { font-size: 0.9em; color: #888; }

        .order-summary-section { position: sticky; top: 100px; height: fit-content; }

        .order-items {
            background: white; border-radius: 20px; padding: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08); border: 1px solid #f0f0f0; margin-bottom: 20px;
        }

        .order-item {
            display: flex; align-items: center; gap: 15px;
            padding: 12px 0; border-bottom: 1px solid #f5f5f5;
        }
        .order-item:last-child { border-bottom: none; }
        .order-item img { width: 55px; height: 55px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .order-item-name { font-weight: 600; color: #333; font-size: 0.95em; }
        .order-item-qty  { font-size: 0.82em; color: #aaa; margin-top: 2px; }
        .order-item-price { color: #667eea; font-weight: 700; margin-left: auto; white-space: nowrap; }

        .order-total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; border-radius: 20px; padding: 28px;
            box-shadow: 0 5px 25px rgba(102,126,234,0.3);
        }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 1.05em; }
        .total-row.final {
            border-top: 2px solid rgba(255,255,255,0.3);
            margin-top: 12px; padding-top: 18px;
            font-size: 1.5em; font-weight: 700;
        }

        .place-order-btn {
            width: 100%; padding: 18px; background: white; color: #667eea;
            border: none; border-radius: 50px; font-weight: 700; font-size: 1.15em;
            cursor: pointer; margin-top: 22px; transition: all 0.3s;
        }
        .place-order-btn:hover { transform: scale(1.02); box-shadow: 0 5px 20px rgba(255,255,255,0.3); }
        .place-order-btn:disabled { opacity: 0.6; cursor: not-allowed; }

        .success-page {
            max-width: 680px; margin: 60px auto; background: white;
            border-radius: 20px; padding: 60px 40px; text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .success-icon  { font-size: 5em; margin-bottom: 20px; }
        .success-title { font-family: 'Playfair Display', serif; font-size: 2.4em; color: #333; margin-bottom: 15px; }
        .success-details { background: #f8f8f8; padding: 22px; border-radius: 15px; margin: 25px 0; text-align: left; }
        .success-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .success-row:last-child { border-bottom: none; }
        .success-actions { display: flex; gap: 15px; justify-content: center; margin-top: 28px; flex-wrap: wrap; }

        @media (max-width: 968px) {
            .checkout-grid { grid-template-columns: 1fr; }
            .order-summary-section { position: static; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <main>
        <div class="checkout-container">
            <div class="page-header">
                <div class="page-subtitle">Almost there</div>
                <h1 class="page-title">Checkout</h1>
                <p class="page-description">Fill in your delivery details and place your order</p>
            </div>

            <?php if ($orderSuccess): ?>
                <div class="success-page">
                    <div class="success-icon">✅</div>
                    <h2 class="success-title">Order Placed!</h2>
                    <p style="color:#666;font-size:1.1em;margin-bottom:10px;">Thank you — your order is confirmed.</p>

                    <div class="success-details">
                        <div class="success-row">
                            <strong>Order Number</strong>
                            <span>#<?php echo htmlspecialchars($orderId); ?></span>
                        </div>
                        <div class="success-row">
                            <strong>Total Amount</strong>
                            <span>UGX <?php echo number_format($total, 0); ?></span>
                        </div>
                        <div class="success-row">
                            <strong>Payment</strong>
                            <span>💵 Pay on Delivery</span>
                        </div>
                    </div>

                    <div style="background:#e8f5e9;padding:18px 22px;border-radius:14px;border-left:4px solid #4caf50;text-align:left;margin-bottom:10px;">
                        <p style="color:#2e7d32;line-height:1.9;margin:0;">
                            📱 We'll call you shortly to confirm delivery details.<br>
                            🚚 Expected delivery: 30–45 minutes.<br>
                            💵 Please have cash ready on delivery.
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
                    <p class="empty-state-description">Add some items before checking out.</p>
                    <a href="search.php" class="btn btn-primary">Browse Menu</a>
                </div>

            <?php else: ?>
                <div class="checkout-grid">
                    <!-- Left: Delivery Info -->
                    <div class="checkout-form-section">
                        <h2 class="section-title">Delivery Information</h2>

                        <form id="checkout-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="0700123456" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Delivery Address</label>
                                <input type="text" id="address" name="address" class="form-control" placeholder="Street, building, landmark..." required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City / Town</label>
                                    <input type="text" id="city" name="city" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email (optional)</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="For order confirmation">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Order Notes (optional)</label>
                                <textarea id="notes" name="notes" class="form-control" rows="3"
                                    placeholder="Any special instructions for your order or delivery..."></textarea>
                            </div>

                            <!-- Pay on Delivery badge -->
                            <div class="pod-badge">
                                <div class="pod-icon">💵</div>
                                <div class="pod-text">
                                    <strong>Pay on Delivery</strong>
                                    <span>Have your cash ready when the rider arrives</span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Right: Order Summary -->
                    <div class="order-summary-section">
                        <div class="order-items">
                            <h3 class="section-title" style="font-size:1.3em;">Order Summary</h3>
                            <?php foreach ($cartItems as $item):
                                $qty       = $item['quantity'] ?? 1;
                                $unitPrice = floatval(str_replace(['UGX','KSh',' ',','], '', $item['price']));
                                $lineTotal = $unitPrice * $qty;
                            ?>
                            <div class="order-item">
                                <img src="../../assets/images/food pics/<?php echo htmlspecialchars($item['image']); ?>"
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div style="flex:1;">
                                    <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="order-item-qty">x<?php echo $qty; ?> · <?php echo htmlspecialchars($item['restaurant']); ?></div>
                                </div>
                                <div class="order-item-price">UGX <?php echo number_format($lineTotal, 0); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-total-section">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span>UGX <?php echo number_format($subtotal, 0); ?></span>
                            </div>
                            <div class="total-row" style="flex-direction:column;gap:8px;">
                                <span>Delivery Zone</span>
                                <select id="zone_select" onchange="updateDeliveryFee(this)"
                                    style="width:100%;padding:10px 14px;border-radius:12px;border:2px solid rgba(255,255,255,0.4);background:rgba(255,255,255,0.2);color:white;font-family:'Poppins',sans-serif;font-size:0.9em;">
                                    <?php foreach ($deliveryZones as $z): ?>
                                    <option value="<?php echo $z['id']; ?>" data-fee="<?php echo $z['fee']; ?>"
                                        <?php echo $z['id'] == $selectedZoneId ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($z['zone_name']); ?> — UGX <?php echo number_format($z['fee'], 0); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="total-row">
                                <span>Delivery Fee</span>
                                <span id="delivery-fee-display">UGX <?php echo number_format($deliveryFee, 0); ?></span>
                            </div>
                            <div class="total-row final">
                                <span>Total</span>
                                <span id="grand-total-display">UGX <?php echo number_format($total, 0); ?></span>
                            </div>

                            <button type="submit" form="checkout-form" class="place-order-btn" id="place-btn">
                                <span id="btn-text">🛵 Place Order</span>
                                <span id="btn-loader" style="display:none;">Placing order...</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script>
    function updateDeliveryFee(select) {
        const fee      = parseInt(select.options[select.selectedIndex].dataset.fee) || 0;
        const subtotal = <?php echo $subtotal; ?>;
        document.getElementById('delivery-fee-display').textContent  = 'UGX ' + fee.toLocaleString();
        document.getElementById('grand-total-display').textContent   = 'UGX ' + (subtotal + fee).toLocaleString();
    }

    document.getElementById('checkout-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn    = document.getElementById('place-btn');
        const text   = document.getElementById('btn-text');
        const loader = document.getElementById('btn-loader');

        btn.disabled      = true;
        text.style.display  = 'none';
        loader.style.display = 'inline';

        const zoneSelect = document.getElementById('zone_select');
        const fee        = parseInt(zoneSelect.options[zoneSelect.selectedIndex].dataset.fee) || 0;

        const payload = {
            name:    document.getElementById('name').value,
            phone:   document.getElementById('phone').value,
            address: document.getElementById('address').value,
            city:    document.getElementById('city').value,
            email:   document.getElementById('email').value,
            notes:   document.getElementById('notes').value,
            zone_id: zoneSelect.value,
            delivery_fee: fee,
            payment_method: 'pay_on_delivery'
        };

        try {
            const res    = await fetch(window.BASE_PATH + 'api/create_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();

            if (result.success) {
                window.location.href = 'checkout.php?success=1&order_id=' + result.order_id;
            } else {
                alert(result.message || 'Failed to place order. Please try again.');
                btn.disabled = false;
                text.style.display  = 'inline';
                loader.style.display = 'none';
            }
        } catch (err) {
            alert('Network error. Please check your connection and try again.');
            btn.disabled = false;
            text.style.display  = 'inline';
            loader.style.display = 'none';
        }
    });
    </script>
</body>
</html>
