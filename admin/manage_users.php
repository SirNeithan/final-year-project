<?php
session_start();
require '../includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/auth/login.php');
    exit();
}

$message = '';

// Handle user role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $userId = intval($_POST['user_id']);
    $newRole = $_POST['role'];
    
    // Prevent admin from changing their own role
    if ($userId == $_SESSION['user_id']) {
        $message = 'You cannot change your own role!';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $userId]);
            $message = 'User role updated successfully!';
        } catch (Exception $e) {
            $message = 'Error updating role: ' . $e->getMessage();
        }
    }
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    
    // Prevent admin from deleting themselves
    if ($userId == $_SESSION['user_id']) {
        $message = 'You cannot delete your own account!';
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $message = 'User deleted successfully!';
        } catch (Exception $e) {
            $message = 'Error deleting user: ' . $e->getMessage();
        }
    }
}

// Get all users with their order statistics
try {
    $stmt = $conn->query("
        SELECT u.*, 
               COUNT(DISTINCT o.id) as total_orders,
               COALESCE(SUM(o.total_amount), 0) as total_spent
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching users: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
        
        .error-message {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease;
            border: 1px solid #f0f0f0;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #333;
            font-size: 1em;
            font-weight: 600;
        }
        
        .admin-container > h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 25px;
            font-family: 'Playfair Display', serif;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .users-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .users-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        
        .users-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .role-badge {
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .role-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .role-user {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .role-select {
            padding: 8px 12px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .role-select:focus {
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
            font-size: 0.9em;
            transition: all 0.3s ease;
            margin-left: 5px;
        }
        
        .update-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .delete-btn {
            padding: 8px 15px;
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9em;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .delete-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(235, 51, 73, 0.4);
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
            
            .users-table {
                font-size: 0.9em;
            }
            
            .users-table th, .users-table td {
                padding: 10px;
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
        <h1>👥 Manage Users</h1>
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
                <div class="message <?php echo strpos($message, 'cannot') !== false ? 'error-message' : ''; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?php echo count($users); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Admin Users</div>
                    <div class="stat-value">
                        <?php echo count(array_filter($users, function($u) { return $u['role'] === 'admin'; })); ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Regular Users</div>
                    <div class="stat-value">
                        <?php echo count(array_filter($users, function($u) { return $u['role'] === 'user'; })); ?>
                    </div>
                </div>
            </div>

            <h2>All Users (<?php echo count($users); ?>)</h2>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($user['username']); ?>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <span style="color: #ff6b35; font-size: 0.85em;">(You)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" class="role-select" 
                                            <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button type="submit" name="update_role" class="update-btn">Update</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td><?php echo $user['total_orders']; ?></td>
                            <td>UGX <?php echo number_format($user['total_spent'], 0); ?></td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" 
                                       class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this user? This will also delete all their orders.')">
                                        Delete
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 0.9em;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>Smart Dine Admin Panel | Contact:0766191751</p>
    </footer>
</body>
</html>
