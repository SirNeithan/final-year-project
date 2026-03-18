<?php
session_start();
require '../includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/auth/login.php');
    exit();
}

// Users online = last_seen within 15 minutes
try {
    $stmt = $conn->query("
        SELECT u.id, u.username, u.email, u.role,
               ua.page, ua.ip_address, ua.last_seen
        FROM user_activity ua
        JOIN users u ON ua.user_id = u.id
        WHERE ua.last_seen >= NOW() - INTERVAL 15 MINUTE
        ORDER BY ua.last_seen DESC
    ");
    $onlineUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $onlineUsers = [];
    $dbError = $e->getMessage();
}

// Recent logins (last 50)
try {
    $stmt = $conn->query("
        SELECT ul.*, u.username, u.email, u.role
        FROM user_logins ul
        JOIN users u ON ul.user_id = u.id
        ORDER BY ul.logged_in_at DESC
        LIMIT 50
    ");
    $recentLogins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $recentLogins = [];
}

// Per-user last activity (all users)
try {
    $stmt = $conn->query("
        SELECT u.id, u.username, u.email, u.role,
               MAX(ua.last_seen) as last_seen,
               COUNT(DISTINCT ua.page) as pages_visited,
               (SELECT COUNT(*) FROM user_logins ul2 WHERE ul2.user_id = u.id) as login_count
        FROM users u
        LEFT JOIN user_activity ua ON u.id = ua.user_id
        GROUP BY u.id
        ORDER BY last_seen DESC
    ");
    $allUserActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $allUserActivity = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #fff; min-height: 100vh; padding: 20px; }

        header {
            background: white; padding: 20px 30px; border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #f0f0f0;
        }
        header h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            font-size: 2em; margin-bottom: 15px;
        }
        header nav ul { list-style: none; display: flex; gap: 20px; flex-wrap: wrap; }
        header nav a { color: #333; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        header nav a:hover { color: #667eea; }

        .admin-container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px; margin-bottom: 30px;
        }
        .stat-card {
            background: white; padding: 25px; border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08); text-align: center; border: 1px solid #f0f0f0;
        }
        .stat-value {
            font-size: 2.2em; font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            margin: 8px 0;
        }
        .stat-label { color: #333; font-size: 0.95em; font-weight: 600; }

        .section-card {
            background: white; border-radius: 20px; padding: 30px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08); border: 1px solid #f0f0f0; margin-bottom: 30px;
        }
        .section-card h2 {
            font-family: 'Playfair Display', serif; font-size: 1.5em;
            margin-bottom: 20px; color: #333;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 13px 15px; text-align: left; font-weight: 600; font-size: 0.9em;
        }
        td { padding: 13px 15px; border-bottom: 1px solid #eee; color: #333; font-size: 0.9em; }
        tr:hover td { background: #fafafa; }

        .online-dot {
            display: inline-block; width: 10px; height: 10px;
            background: #11998e; border-radius: 50%; margin-right: 6px;
            box-shadow: 0 0 0 3px rgba(17,153,142,0.25);
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%,100% { box-shadow: 0 0 0 3px rgba(17,153,142,0.25); }
            50%      { box-shadow: 0 0 0 6px rgba(17,153,142,0.1); }
        }

        .role-badge {
            padding: 4px 12px; border-radius: 12px; font-size: 0.8em; font-weight: 600;
        }
        .role-admin { background: linear-gradient(135deg,#667eea,#764ba2); color: white; }
        .role-user  { background: linear-gradient(135deg,#11998e,#38ef7d); color: white; }

        .page-label {
            font-size: 0.8em; color: #888; font-family: monospace;
            background: #f5f5f5; padding: 3px 8px; border-radius: 6px;
        }

        .never { color: #ccc; font-style: italic; }

        .alert-box {
            background: #fff3cd; border-left: 4px solid #ffc107;
            padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; color: #856404;
        }

        footer {
            background: #f8f8f8; padding: 20px; border-radius: 20px;
            margin-top: 30px; text-align: center; color: #333; font-weight: 500; border: 1px solid #f0f0f0;
        }

        @media (max-width: 768px) {
            table { font-size: 0.8em; }
            th, td { padding: 8px; }
        }
    </style>
</head>
<body>
    <header>
        <h1><i class="ri-bar-chart-line"></i> User Activity</h1>
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

            <?php if (isset($dbError)): ?>
            <div class="alert-box">
                <i class="ri-alert-line"></i> Activity tables not found. Run <strong>data/missing_features.sql</strong> to create them.<br>
                <small><?php echo htmlspecialchars($dbError); ?></small>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label"><i class="ri-checkbox-circle-line"></i> Online Now</div>
                    <div class="stat-value"><?php echo count($onlineUsers); ?></div>
                    <div style="color:#999;font-size:0.8em;">last 15 min</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="ri-key-line"></i> Logins Today</div>
                    <div class="stat-value">
                        <?php
                        $today = array_filter($recentLogins, fn($l) => date('Y-m-d', strtotime($l['logged_in_at'])) === date('Y-m-d'));
                        echo count($today);
                        ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="ri-time-line"></i> Total Logins Logged</div>
                    <div class="stat-value"><?php echo count($recentLogins); ?>+</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="ri-user-line"></i> Active Users (ever)</div>
                    <div class="stat-value">
                        <?php echo count(array_filter($allUserActivity, fn($u) => $u['last_seen'] !== null)); ?>
                    </div>
                </div>
            </div>

            <!-- Online Now -->
            <div class="section-card">
                <h2><span class="online-dot"></span>Currently Online (last 15 minutes)</h2>
                <?php if (empty($onlineUsers)): ?>
                    <p style="color:#999;text-align:center;padding:20px 0;">No users active in the last 15 minutes.</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Current Page</th>
                            <th>IP Address</th>
                            <th>Last Seen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($onlineUsers as $u): ?>
                        <tr>
                            <td>
                                <span class="online-dot"></span>
                                <strong><?php echo htmlspecialchars($u['username']); ?></strong><br>
                                <small style="color:#999;"><?php echo htmlspecialchars($u['email']); ?></small>
                            </td>
                            <td><span class="role-badge role-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                            <td><span class="page-label"><?php echo htmlspecialchars($u['page']); ?></span></td>
                            <td><?php echo htmlspecialchars($u['ip_address'] ?? '—'); ?></td>
                            <td><?php echo date('H:i:s', strtotime($u['last_seen'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- All Users Activity Summary -->
            <div class="section-card">
                <h2><i class="ri-group-line"></i> All Users — Activity Summary</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Total Logins</th>
                            <th>Pages Visited</th>
                            <th>Last Seen</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUserActivity as $u):
                            $isOnline = $u['last_seen'] && strtotime($u['last_seen']) >= strtotime('-15 minutes');
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($u['username']); ?></strong><br>
                                <small style="color:#999;"><?php echo htmlspecialchars($u['email']); ?></small>
                            </td>
                            <td><span class="role-badge role-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                            <td><?php echo (int)$u['login_count']; ?></td>
                            <td><?php echo (int)$u['pages_visited']; ?></td>
                            <td>
                                <?php if ($u['last_seen']): ?>
                                    <?php echo date('M j, Y H:i', strtotime($u['last_seen'])); ?>
                                <?php else: ?>
                                    <span class="never">Never</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($isOnline): ?>
                                    <span style="color:#11998e;font-weight:600;"><span class="online-dot"></span>Online</span>
                                <?php elseif ($u['last_seen']): ?>
                                    <span style="color:#999;">Offline</span>
                                <?php else: ?>
                                    <span style="color:#ccc;">No activity</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Logins -->
            <div class="section-card">
                <h2><i class="ri-key-line"></i> Recent Logins</h2>
                <?php if (empty($recentLogins)): ?>
                    <p style="color:#999;text-align:center;padding:20px 0;">No login records yet.</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>IP Address</th>
                            <th>Logged In At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLogins as $login): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($login['username']); ?></strong><br>
                                <small style="color:#999;"><?php echo htmlspecialchars($login['email']); ?></small>
                            </td>
                            <td><span class="role-badge role-<?php echo $login['role']; ?>"><?php echo ucfirst($login['role']); ?></span></td>
                            <td><?php echo htmlspecialchars($login['ip_address'] ?? '—'); ?></td>
                            <td><?php echo date('M j, Y H:i:s', strtotime($login['logged_in_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <footer>
        <p>SmartDine Hub Admin Panel</p>
    </footer>

    <script>
    // Auto-refresh the online users section every 30 seconds
    setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>
