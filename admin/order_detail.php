<?php
session_start();
require '../includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/auth/login.php');
    exit();
}

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get order details
try {
    $stmt = $conn->prepare("SELECT o.*, u.username, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: manage_orders.php');
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching order: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $orderId; ?> - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }
        
        header {
            background: white;
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border: 1px solid #f0f0f0;
        }
        
        header h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2em;
            margin-bottom: 15px;
        }
        
        header nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        header nav a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        header nav a:hover {
            color: #667eea;
        }
        
        .admin-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .back-btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 15px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .order-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }
        
        .order-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .order-header h2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2em;
            margin-bottom: 10px;
            font-family: 'Playfair Display', serif;
        }
        
        .order-header p {
            color: #666;
            margin: 5px 0;
        }
        
        .order-header strong {
            color: #333;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-section {
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 15px;
        }
        
        .info-section h3 {
            margin-top: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.1em;
            margin-bottom: 12px;
            font-weight: 600;
            font-family: 'Playfair Display', serif;
        }
        
        .info-section p {
            margin: 8px 0;
            color: #555;
            font-size: 0.95em;
        }
        
        .order-card > h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.3em;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
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
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        
        footer {
            background: #f8f8f8;
            padding: 20px;
            border-radius: 20px;
            margin-top: 30px;
            text-align: center;
            color: #333;
            font-weight: 500;
            border: 1px solid #f0f0f0;
        }
        
        @media (max-width: 768px) {
            header nav ul {
                flex-direction: column;
                gap: 10px;
            }
        }
    
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }
        
        header {
            padding: 15px;
        }
        
        header h1 {
            font-size: 1.5em;
        }
        
        header nav ul {
            flex-direction: column;
            gap: 8px;
        }
        
        header nav a {
            font-size: 0.9em;
            padding: 8px 12px;
        }
        
        .admin-container {
            padding: 10px;
        }
        
        .admin-container > h2 {
            font-size: 1.5em;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .stat-card {
            padding: 20px;
        }
        
        .stat-value {
            font-size: 2em;
        }
        
        .orders-table, .users-table, .products-table {
            font-size: 0.85em;
        }
        
        .orders-table th, .orders-table td,
        .users-table th, .users-table td,
        .products-table th, .products-table td {
            padding: 8px;
        }
        
        .add-form {
            padding: 20px;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            font-size: 0.95em;
            padding: 10px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .items-table {
            font-size: 0.85em;
        }
    }
    
    @media (max-width: 480px) {
        header h1 {
            font-size: 1.3em;
        }
        
        header nav a {
            font-size: 0.85em;
            padding: 6px 10px;
        }
        
        .admin-container > h2 {
            font-size: 1.3em;
        }
        
        .stat-card {
            padding: 15px;
        }
        
        .stat-value {
            font-size: 1.8em;
        }
        
        .stat-label {
            font-size: 0.95em;
        }
        
        .orders-table, .users-table, .products-table {
            font-size: 0.75em;
        }
        
        .orders-table th, .orders-table td,
        .users-table th, .users-table td,
        .products-table th, .products-table td {
            padding: 6px;
        }
        
        .status-select, .role-select {
            font-size: 0.85em;
            padding: 6px;
        }
        
        .update-btn, .delete-btn, .view-btn, .action-btn {
            font-size: 0.8em;
            padding: 6px 10px;
        }
        
        .add-form {
            padding: 15px;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            font-size: 0.9em;
            padding: 8px;
        }
        
        .btn {
            font-size: 0.9em;
            padding: 10px 20px;
        }
    }
</style>
</head>
<body>
    <header>
        <h1>📋 Order Details</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="../home.php">View Site</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <a href="manage_orders.php" class="back-btn">← Back to Orders</a>
            
            <div class="order-card">
                <div class="order-header">
                    <h2>Order #<?php echo $order['id']; ?></h2>
                    <p>Status: <strong><?php echo ucfirst($order['status']); ?></strong></p>
                    <p>Placed: <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
                </div>

                <div class="info-grid">
                    <div class="info-section">
                        <h3>Customer</h3>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    </div>
                    
                    <div class="info-section">
                        <h3>Delivery</h3>
                        <p><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                        <p><?php echo htmlspecialchars($order['delivery_city']); ?>, <?php echo htmlspecialchars($order['delivery_zip']); ?></p>
                    </div>
                    
                    <div class="info-section">
                        <h3>Payment</h3>
                        <p><strong>Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                        <p><strong>Total:</strong> UGX <?php echo number_format($order['total_amount'], 0); ?></p>
                    </div>
                </div>

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
                            // Remove currency symbols and commas from price
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
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer>
        <p>Smart Dine Admin Panel</p>
    </footer>
</body>
</html>
