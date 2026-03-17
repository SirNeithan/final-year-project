<?php
session_start();
require '../../includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get user's orders
try {
    $stmt = $conn->prepare("
        SELECT o.*, COUNT(oi.id) as item_count 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching orders: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Smart Dine</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/elegant-theme.css">
    <style>
        body {
            background: #ffffff;
        }
        
        .orders-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .order-card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
            transition: all 0.3s;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0,0,0,0.12);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f5f5f5;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .order-id {
            font-family: 'Playfair Display', serif;
            font-size: 1.6em;
            font-weight: 600;
            color: #333;
        }
        
        .order-status {
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        
        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .detail-item {
            padding: 20px;
            background: #f8f8f8;
            border-radius: 15px;
        }
        
        .detail-label {
            font-size: 0.85em;
            color: #999;
            margin-bottom: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 1.15em;
        }
        
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .order-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <main>
        <div class="orders-container">
            <div class="page-header">
                <div class="page-subtitle">Order History</div>
                <h1 class="page-title">My Orders</h1>
                <p class="page-description">Track and manage all your orders in one place</p>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h2 class="empty-state-title">No Orders Yet</h2>
                    <p class="empty-state-description">You haven't placed any orders yet. Start exploring our menu!</p>
                    <a href="../../home.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order #<?php echo $order['id']; ?></div>
                            <div class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </div>
                        </div>
                        
                        <div class="order-details">
                            <div class="detail-item">
                                <div class="detail-label">Order Date</div>
                                <div class="detail-value"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Total Amount</div>
                                <div class="detail-value">UGX <?php echo number_format($order['total_amount'], 0); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Items</div>
                                <div class="detail-value"><?php echo $order['item_count']; ?> item(s)</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Delivery City</div>
                                <div class="detail-value"><?php echo htmlspecialchars($order['delivery_city']); ?></div>
                            </div>
                        </div>
                        
                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-primary">View Details</a>
                        <a href="order_details.php?id=<?php echo $order['id']; ?>&reorder=1" class="btn btn-secondary" style="margin-left:10px;" onclick="return confirm('Add all items from this order to your cart?')">🔄 Reorder</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
