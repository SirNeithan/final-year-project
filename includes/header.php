<?php
// Determine the base path for navigation
if (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) {
    $basePath = '../../';
} else {
    $basePath = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Smart Dine'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            color: #333;
        }
        
        header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        header h1 {
            font-size: 2em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95em;
        }
        
        .admin-badge {
            background: #ffd700;
            color: #333;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75em;
            margin-left: 8px;
            font-weight: 700;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }
        
        nav {
            width: 100%;
            margin-top: 15px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        
        nav ul li a {
            color: #667eea;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 20px;
            transition: all 0.3s;
            font-weight: 500;
            background: rgba(102, 126, 234, 0.1);
        }
        
        nav ul li a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
        }
        
        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        footer {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            color: #333;
            text-align: center;
            padding: 30px;
            margin-top: 50px;
            font-weight: 500;
        }
        
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            display: none;
            z-index: 2000;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                padding: 15px;
            }
            
            header h1 {
                font-size: 1.5em;
            }
            
            .user-info {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            
            .user-badge {
                font-size: 0.85em;
                padding: 8px 15px;
            }
            
            .logout-btn {
                font-size: 0.9em;
                padding: 8px 16px;
            }
            
            nav ul {
                gap: 8px;
                padding: 0 10px;
            }
            
            nav ul li a {
                font-size: 0.85em;
                padding: 8px 12px;
                white-space: nowrap;
            }
        }
        
        @media (max-width: 480px) {
            header h1 {
                font-size: 1.3em;
            }
            
            .header-content {
                padding: 10px;
            }
            
            nav ul {
                gap: 6px;
                justify-content: center;
            }
            
            nav ul li a {
                font-size: 0.75em;
                padding: 6px 10px;
            }
            
            .user-badge {
                font-size: 0.8em;
                padding: 6px 12px;
            }
            
            .logout-btn {
                font-size: 0.85em;
                padding: 6px 14px;
            }
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    
    <header>
        <div class="header-content">
            <h1><?php echo $headerTitle ?? 'Smart Dine'; ?></h1>
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <span class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <span class="admin-badge">ADMIN</span>
                    <?php endif; ?>
                </span>
                <a href="<?php echo $basePath; ?>pages/auth/logout.php" class="logout-btn">🚪 Logout</a>
            </div>
            <?php endif; ?>
        </div>
        <nav>
            <ul>
                <li><a href="<?php echo $basePath; ?>home.php">🏠 Home</a></li>
                <?php if (isset($restaurant) && $restaurant): ?>
                    <li><a href="<?php echo $basePath; ?>pages/categories/appetizers.php?restaurant=<?php echo urlencode($restaurant); ?>">🥗 Appetizers</a></li>
                    <li><a href="<?php echo $basePath; ?>pages/categories/main-courses.php?restaurant=<?php echo urlencode($restaurant); ?>">🍽️ Main Courses</a></li>
                    <li><a href="<?php echo $basePath; ?>pages/categories/desserts.php?restaurant=<?php echo urlencode($restaurant); ?>">🍰 Desserts</a></li>
                    <li><a href="<?php echo $basePath; ?>pages/categories/beverages.php?restaurant=<?php echo urlencode($restaurant); ?>">🥤 Beverages</a></li>
                <?php endif; ?>
                <li><a href="<?php echo $basePath; ?>pages/user/search.php">🔍 Search</a></li>
                <li><a href="<?php echo $basePath; ?>pages/user/profile.php">👤 Profile</a></li>
                <li><a href="<?php echo $basePath; ?>pages/orders/orders.php">📦 Orders</a></li>
                <li><a href="<?php echo $basePath; ?>pages/user/cart.php">🛒 Cart (<span id="cart-count">0</span>)</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="<?php echo $basePath; ?>admin/index.php" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333;">⚙️ Admin</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
