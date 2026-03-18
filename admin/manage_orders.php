<?php
session_start();
require '../includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/auth/login.php');
    exit();
}

$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = $_POST['status'];
    
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        $message = 'Order status updated successfully!';
    } catch (Exception $e) {
        $message = 'Error updating status: ' . $e->getMessage();
    }
}

// Get all orders
try {
    $stmt = $conn->query("
        SELECT o.*, u.username, COUNT(oi.id) as item_count
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
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
    <title>Manage Orders - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .message {
            padding: 15px 20px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 15px;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3);
        }
        
        .admin-container > h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 25px;
            font-family: 'Playfair Display', serif;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .orders-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .orders-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        
        .orders-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .status-select {
            padding: 8px 12px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .status-select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .update-btn {
            padding: 8px 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 5px;
        }
        
        .update-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .view-btn {
            padding: 8px 15px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .view-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
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
            
            .orders-table {
                font-size: 0.9em;
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
        <h1><i class="ri-box-line"></i> Manage Orders</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="user_activity.php">User Activity</a></li>
                <li><a href="../home.php">View Site</a></li>
                <li><a href="../pages/auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <h2>All Orders</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo $order['item_count']; ?> items</td>
                            <td>UGX <?php echo number_format($order['total_amount'], 0); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="status-select">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="out_for_delivery" <?php echo $order['status'] === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="update-btn">Update</button>
                                </form>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="view-btn">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>Smart Dine Admin Panel</p>
    </footer>
</body>
</html>
