<?php
/**
 * Header Include File
 * 
 * This file contains the common header section for all pages in the Smart Dine application.
 * It includes:
 * - HTML head with meta tags and fonts
 * - Navigation bar with links to all major sections
 * - User information display (username, role, logout button)
 * - Cart counter badge
 * - Responsive design for mobile devices
 * 
 * Usage: include 'includes/header.php';
 */

// Determine the base path for navigation links based on directory depth
$selfPath = $_SERVER['PHP_SELF'];
if (preg_match('#/pages/[^/]+/[^/]+$#', $selfPath)) {
    $basePath = '../../';
} elseif (preg_match('#/pages/[^/]+$#', $selfPath)) {
    $basePath = '../';
} else {
    $basePath = '';
}

// Absolute web root for AJAX — works in any subfolder (e.g. /final-year-project/)
// Start from the current directory path so root-level pages do not keep the script filename.
$currentDir = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));

if ($currentDir === '/' || $currentDir === '.') {
    $currentDir = '';
}

// Strip nested application folders back to the project root.
$projectRoot = preg_replace('#/(pages|api|admin|includes|assets)(/.*)?$#', '', $currentDir);
$webRoot = ($projectRoot === '' ? '/' : rtrim($projectRoot, '/') . '/');

// Track user activity (silently — never breaks the page)
if (isset($_SESSION['user_id']) && isset($conn)) {
    try {
        $page = substr($_SERVER['PHP_SELF'], 0, 191);
        $ip   = $_SERVER['REMOTE_ADDR'] ?? null;
        $conn->prepare("
            INSERT INTO user_activity (user_id, page, ip_address, last_seen)
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE last_seen = NOW(), ip_address = VALUES(ip_address)
        ")->execute([$_SESSION['user_id'], $page, $ip]);
    } catch (Exception $e) { /* table may not exist yet — ignore */ }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Smart Dine'; ?></title>
    <!-- Google Fonts - Poppins for modern, clean typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Reset - Remove default browser styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff; /* Clean white background */
            min-height: 100vh;
            color: #333;
        }
        
        /* Header Styles - Sticky navigation bar */
        header {
            background: white; /* Solid white */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky; /* Stays at top when scrolling */
            top: 0;
            z-index: 1000; /* Ensures header stays above other content */
            border-bottom: 1px solid #f0f0f0;
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
        
        /* Logo Container */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }
        
        /* Logo Icon */
        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8em;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        /* Logo Text */
        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        
        .logo-text .brand-name {
            font-size: 1.8em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-text .brand-tagline {
            font-size: 0.7em;
            color: #999;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        /* Legacy h1 support (hidden when logo is used) */
        header h1 {
            font-size: 2em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        /* User Information Display */
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* User Badge - Shows username */
        .user-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95em;
        }
        
        /* Admin Badge - Shows if user is admin */
        .admin-badge {
            background: #ffd700; /* Gold color for admin */
            color: #333;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75em;
            margin-left: 8px;
            font-weight: 700;
        }
        
        /* Logout Button */
        .logout-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); /* Pink gradient */
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s; /* Smooth animation */
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        }
        
        .logout-btn:hover {
            transform: translateY(-2px); /* Lift effect on hover */
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }
        
        /* Navigation Menu */
        nav {
            width: 100%;
            margin-top: 15px;
        }
        
        nav ul {
            list-style: none; /* Remove bullet points */
            display: flex;
            flex-wrap: wrap; /* Allow wrapping on small screens */
            gap: 10px;
            justify-content: center;
        }
        
        /* Navigation Links */
        nav ul li a {
            color: #667eea;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 20px;
            transition: all 0.3s;
            font-weight: 500;
            background: rgba(102, 126, 234, 0.1); /* Light purple background */
        }
        
        nav ul li a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px); /* Lift effect */
        }
        
        /* Main Content Area */
        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        /* Footer Styles */
        footer {
            background: #f8f8f8;
            color: #333;
            text-align: center;
            padding: 30px;
            margin-top: 50px;
            font-weight: 500;
            border-top: 1px solid #e8e8e8;
        }
        
        /* Notification Toast - Appears in top right corner */
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            display: none; /* Hidden by default */
            z-index: 2000; /* Above everything else */
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }
        
        /* Slide-in animation for notifications */
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
        
        /* Responsive Design - Tablet and Mobile */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                padding: 15px;
            }
            
            .logo-icon {
                width: 45px;
                height: 45px;
                font-size: 1.5em;
            }
            
            .logo-text .brand-name {
                font-size: 1.5em;
            }
            
            .logo-text .brand-tagline {
                font-size: 0.65em;
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
        
        /* Extra Small Screens - Mobile Phones */
        @media (max-width: 480px) {
            .logo-icon {
                width: 40px;
                height: 40px;
                font-size: 1.3em;
            }
            
            .logo-text .brand-name {
                font-size: 1.3em;
            }
            
            .logo-text .brand-tagline {
                font-size: 0.6em;
            }
            
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
    <!-- Notification toast for displaying messages to users -->
    <div id="notification"></div>
    
    <header>
        <div class="header-content">
            <!-- Site Logo -->
            <a href="<?php echo $basePath; ?>home.php" class="logo">
                <div class="logo-icon">🍽️</div>
                <div class="logo-text">
                    <span class="brand-name">SmartDine Hub</span>
                    <span class="brand-tagline">Delicious Delivered</span>
                </div>
            </a>
            
            <!-- User information section (only shown if user is logged in) -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <span class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <!-- Show admin badge if user is an admin -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <span class="admin-badge">ADMIN</span>
                    <?php endif; ?>
                </span>
                <a href="<?php echo $basePath; ?>pages/auth/logout.php" class="logout-btn">🚪 Logout</a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Navigation menu -->
        <nav>
            <ul>
                <!-- Main navigation links -->
                <li><a href="<?php echo $basePath; ?>home.php">🏠 Home</a></li>
                
                <!-- Category links (only shown if restaurant is set) -->
                <?php if (isset($restaurant) && $restaurant): ?>
                    <li><a href="<?php echo $basePath; ?>pages/categories/appetizers.php?restaurant=<?php echo urlencode($restaurant); ?>">🥗 Appetizers</a></li>
                    <li><a href="<?php echo $basePath; ?>pages/categories/main-courses.php?restaurant=<?php echo urlencode($restaurant); ?>">🍽️ Main Courses</a></li>
                    <li><a href="<?php echo $basePath; ?>pages/categories/desserts.php?restaurant=<?php echo urlencode($restaurant); ?>">🍰 Desserts</a></li>
                    <li><a href="<?php echo $basePath; ?>pages/categories/beverages.php?restaurant=<?php echo urlencode($restaurant); ?>">🥤 Beverages</a></li>
                <?php endif; ?>
                
                <!-- User-specific links -->
                <li><a href="<?php echo $basePath; ?>pages/user/search.php">🔍 Search</a></li>
                <li><a href="<?php echo $basePath; ?>pages/about.php">ℹ️ About</a></li>
                <li><a href="<?php echo $basePath; ?>pages/user/profile.php">👤 Profile</a></li>
                <li><a href="<?php echo $basePath; ?>pages/orders/orders.php">📦 Orders</a></li>
                
                <!-- Cart link with dynamic item count -->
                <li><a href="<?php echo $basePath; ?>pages/user/cart.php">🛒 Cart (<span id="cart-count">0</span>)</a></li>
                
                <!-- Admin link (only shown for admin users) -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="<?php echo $basePath; ?>admin/index.php" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333;">⚙️ Admin</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <!-- Main content area (closed in footer.php) -->
    <main>
    <script>
        window.BASE_PATH = '<?php echo $webRoot; ?>';
        console.log('[SmartDine] BASE_PATH =', window.BASE_PATH);
    </script>
