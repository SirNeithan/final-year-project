<?php
session_start();
require '../../includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get order details
try {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: orders.php');
        exit();
    }
    
    // Get order items
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching order details: ' . $e->getMessage());
}

// Handle reorder
if (isset($_GET['reorder']) && $_GET['reorder'] == 1) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    foreach ($orderItems as $item) {
        $alreadyIn = false;
        foreach ($_SESSION['cart'] as $c) {
            if ($c['id'] == $item['product_id'] && $c['restaurant'] == $item['restaurant']) {
                $alreadyIn = true; break;
            }
        }
        if (!$alreadyIn) {
            $_SESSION['cart'][] = ['id' => $item['product_id'], 'restaurant' => $item['restaurant'], 'quantity' => $item['quantity']];
        }
    }
    header('Location: ../../pages/user/cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $orderId; ?> - Smart Dine</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/elegant-theme.css">
    <style>
        body {
            background: #ffffff;
        }
        
        .order-details-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 30px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
        }
        
        .order-summary {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .order-header {
            border-bottom: 2px solid #f5f5f5;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }
        
        .order-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2em;
            color: #333;
            margin-bottom: 10px;
        }
        
        .order-status {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9em;
            margin-left: 15px;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
            color: white;
        }
        
        .status-processing {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .status-cancelled {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .info-section {
            padding: 25px;
            background: #f8f8f8;
            border-radius: 15px;
        }
        
        .info-section h3 {
            font-family: 'Playfair Display', serif;
            color: #333;
            font-size: 1.3em;
            margin-bottom: 15px;
        }
        
        .info-section p {
            margin: 10px 0;
            color: #666;
            line-height: 1.6;
        }
        
        .info-section strong {
            color: #333;
            font-weight: 600;
        }
        
        .items-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8em;
            color: #333;
            margin-bottom: 25px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .items-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px;
            text-align: left;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 18px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }
        
        .items-table tr:hover {
            background-color: #f8f8f8;
        }
        
        .total-row {
            font-weight: 700;
            font-size: 1.3em;
            background: #f8f8f8;
        }
        
        @media (max-width: 768px) {
            .items-table {
                font-size: 0.9em;
            }
            
            .items-table th,
            .items-table td {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <main>
        <div class="order-details-container">
            <a href="orders.php" class="back-link">← Back to Orders</a>
            
            <div class="order-summary">
                <div class="order-header">
                    <h2>
                        Order #<?php echo $order['id']; ?>
                        <span class="order-status status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </h2>
                    <p style="color: #999; font-size: 1.05em;">
                        Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                    </p>

                    <!-- Order status timeline -->
                    <?php
                    $steps = ['pending' => '🕐 Pending', 'processing' => '👨‍🍳 Processing', 'out_for_delivery' => '🚚 Out for Delivery', 'completed' => '✅ Delivered'];
                    $statusOrder = array_keys($steps);
                    $currentIdx = array_search($order['status'], $statusOrder);
                    if ($currentIdx === false) $currentIdx = 0;
                    ?>
                    <div style="display:flex; gap:0; margin-top:25px; align-items:center; flex-wrap:wrap;">
                    <?php foreach ($steps as $key => $label):
                        $idx = array_search($key, $statusOrder);
                        $done = $idx <= $currentIdx;
                        $active = $idx === $currentIdx;
                    ?>
                        <div style="display:flex; align-items:center; gap:0;">
                            <div style="text-align:center; min-width:110px;">
                                <div style="width:36px;height:36px;border-radius:50%;margin:0 auto 6px;display:flex;align-items:center;justify-content:center;font-size:1.1em;
                                    background:<?php echo $done ? 'linear-gradient(135deg,#667eea,#764ba2)' : '#e0e0e0'; ?>;
                                    color:<?php echo $done ? 'white' : '#999'; ?>;
                                    box-shadow:<?php echo $active ? '0 0 0 4px rgba(102,126,234,0.3)' : 'none'; ?>;">
                                    <?php echo $done ? '✓' : ($idx + 1); ?>
                                </div>
                                <div style="font-size:0.78em;font-weight:<?php echo $active ? '700' : '500'; ?>;color:<?php echo $active ? '#667eea' : '#999'; ?>;">
                                    <?php echo $label; ?>
                                </div>
                            </div>
                            <?php if ($idx < count($steps) - 1): ?>
                            <div style="flex:1;height:3px;min-width:30px;background:<?php echo $idx < $currentIdx ? 'linear-gradient(135deg,#667eea,#764ba2)' : '#e0e0e0'; ?>;margin-bottom:22px;"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>

                    <div style="margin-top:20px;">
                        <a href="?id=<?php echo $orderId; ?>&reorder=1" class="btn btn-secondary" onclick="return confirm('Add all items to cart?')">🔄 Reorder</a>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-section">
                        <h3>Customer Information</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                        <?php if (!empty($order['phone'])): ?>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="info-section">
                        <h3>Delivery Address</h3>
                        <p><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                        <p><?php echo htmlspecialchars($order['delivery_city']); ?></p>
                    </div>
                    
                    <div class="info-section">
                        <h3>Payment Information</h3>
                        <p><strong>Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                        <p><strong>Total:</strong> UGX <?php echo number_format($order['total_amount'], 0); ?></p>
                        <?php if (!empty($order['transaction_id'])): ?>
                        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($order['transaction_id']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="items-section">
                    <h3>Order Items</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Restaurant</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): 
                                $priceNum = floatval(str_replace(['UGX', 'KSh', ' ', ','], '', $item['product_price']));
                                $subtotal = $priceNum * $item['quantity'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['restaurant']); ?></td>
                                    <td><?php echo $item['product_price']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>UGX <?php echo number_format($subtotal, 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-row">
                                <td colspan="4" style="text-align: right;">Total:</td>
                                <td>UGX <?php echo number_format($order['total_amount'], 0); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
