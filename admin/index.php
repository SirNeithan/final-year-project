<?php
session_start();
require '../includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/auth/login.php');
    exit();
}

// Get statistics
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM orders");
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM products");
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Recent orders
    $stmt = $conn->query("
        SELECT o.*, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC 
        LIMIT 10
    ");
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching statistics: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Dine</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
        
        header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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
        
        .admin-container > h2 {
            color: white;
            font-size: 1.8em;
            margin-bottom: 25px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2.5em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #333;
            font-size: 1.1em;
            font-weight: 600;
        }
        
        .orders-section {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .orders-section h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 0.85em;
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
        
        .action-btn {
            padding: 8px 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .action-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        footer {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 30px;
            text-align: center;
            color: #333;
            font-weight: 500;
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
        <h1>🎯 Admin Dashboard</h1>
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
            <h2>Dashboard Overview</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo $totalOrders; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?php echo $totalUsers; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Products</div>
                    <div class="stat-value"><?php echo $totalProducts; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">UGX <?php echo number_format($totalRevenue, 0); ?></div>
                </div>
            </div>

            <div class="orders-section">
                <h3>Recent Orders</h3>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td>UGX <?php echo number_format($order['total_amount'], 0); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="action-btn">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer>
        <p>Smart Dine Admin Panel | Contact: +123-456-7890</p>
    </footer>
</body>
</html>
