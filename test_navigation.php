<?php
/**
 * Navigation Test Page
 * This page helps verify that all links are working correctly after reorganization
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Test - Smart Dine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 { color: #667eea; }
        .section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        a {
            display: block;
            padding: 10px;
            margin: 5px 0;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background: #764ba2;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>🧪 Smart Dine Navigation Test</h1>
    
    <div class="section">
        <h2>Main Pages</h2>
        <a href="index.php" target="_blank">Landing Page (index.php)</a>
        <a href="home.php" target="_blank">Home Dashboard (home.php)</a>
    </div>
    
    <div class="section">
        <h2>Authentication Pages</h2>
        <a href="pages/auth/login.php" target="_blank">Login</a>
        <a href="pages/auth/register.php" target="_blank">Register</a>
        <a href="pages/auth/logout.php" target="_blank">Logout</a>
    </div>
    
    <div class="section">
        <h2>User Pages</h2>
        <a href="pages/user/profile.php" target="_blank">Profile</a>
        <a href="pages/user/cart.php" target="_blank">Cart</a>
        <a href="pages/user/checkout.php" target="_blank">Checkout</a>
        <a href="pages/user/search.php" target="_blank">Search</a>
    </div>
    
    <div class="section">
        <h2>Category Pages</h2>
        <a href="pages/categories/appetizers.php" target="_blank">Appetizers</a>
        <a href="pages/categories/main-courses.php" target="_blank">Main Courses</a>
        <a href="pages/categories/desserts.php" target="_blank">Desserts</a>
        <a href="pages/categories/beverages.php" target="_blank">Beverages</a>
    </div>
    
    <div class="section">
        <h2>Order Pages</h2>
        <a href="pages/orders/orders.php" target="_blank">My Orders</a>
        <a href="pages/orders/order_details.php?id=1" target="_blank">Order Details (Sample)</a>
    </div>
    
    <div class="section">
        <h2>Admin Panel</h2>
        <a href="admin/index.php" target="_blank">Admin Dashboard</a>
        <a href="admin/manage_orders.php" target="_blank">Manage Orders</a>
        <a href="admin/manage_products.php" target="_blank">Manage Products</a>
        <a href="admin/manage_users.php" target="_blank">Manage Users</a>
    </div>
    
    <div class="section">
        <h2>File Structure Check</h2>
        <?php
        $requiredFiles = [
            'index.php' => 'Landing Page',
            'home.php' => 'Home Dashboard',
            'pages/auth/login.php' => 'Login',
            'pages/auth/register.php' => 'Register',
            'pages/user/profile.php' => 'Profile',
            'pages/user/cart.php' => 'Cart',
            'pages/categories/appetizers.php' => 'Appetizers',
            'pages/orders/orders.php' => 'Orders',
            'admin/index.php' => 'Admin Dashboard',
            'includes/connect.php' => 'Database Connection',
            'includes/header.php' => 'Header Include',
            'includes/footer.php' => 'Footer Include',
        ];
        
        foreach ($requiredFiles as $file => $name) {
            if (file_exists($file)) {
                echo "<p class='success'>✓ $name ($file)</p>";
            } else {
                echo "<p class='error'>✗ $name ($file) - MISSING!</p>";
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>Instructions</h2>
        <p>1. Click each link above to test if pages load correctly</p>
        <p>2. Check if authentication redirects work (protected pages should redirect to login)</p>
        <p>3. Verify that navigation menus work on each page</p>
        <p>4. Test login functionality with your credentials</p>
        <p>5. Once everything works, you can delete this test file</p>
    </div>
</body>
</html>
