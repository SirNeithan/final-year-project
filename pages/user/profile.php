<?php
session_start();
require '../../includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$messageType = 'success';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email    = trim($_POST['email']);
    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone'] ?? '');

    try {
        // Check username not taken by someone else
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check->execute([$username, $userId]);
        if ($check->fetch()) {
            $message = 'That username is already taken.';
            $messageType = 'error';
        } else {
            $stmt = $conn->prepare("UPDATE users SET email = ?, username = ?, phone = ? WHERE id = ?");
            $stmt->execute([$email, $username, $phone, $userId]);
            $_SESSION['username'] = $username;
            $message = 'Profile updated successfully!';
            $messageType = 'success';
        }
    } catch (Exception $e) {
        // phone column may not exist yet — try without it
        try {
            $stmt = $conn->prepare("UPDATE users SET email = ?, username = ? WHERE id = ?");
            $stmt->execute([$email, $username, $userId]);
            $_SESSION['username'] = $username;
            $message = 'Profile updated (phone field not available in DB).';
            $messageType = 'success';
        } catch (Exception $e2) {
            $message = 'Error updating profile: ' . $e2->getMessage();
            $messageType = 'error';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword === $confirmPassword) {
        try {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $userId]);
                $message = 'Password changed successfully!';
                $messageType = 'success';
            } else {
                $message = 'Current password is incorrect.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error changing password: ' . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = 'New passwords do not match.';
        $messageType = 'error';
    }
}

// Get user info
try {
    $stmt = $conn->prepare("SELECT username, email, role, created_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Try to get phone too
    try {
        $stmt2 = $conn->prepare("SELECT phone FROM users WHERE id = ?");
        $stmt2->execute([$userId]);
        $extra = $stmt2->fetch(PDO::FETCH_ASSOC);
        $user['phone'] = $extra['phone'] ?? '';
    } catch (Exception $e) {
        $user['phone'] = '';
    }
} catch (Exception $e) {
    die('Error fetching user data: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Smart Dine</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/elegant-theme.css">
    <style>
        body {
            background: #ffffff;
        }
        
        .profile-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .message {
            padding: 20px 30px;
            margin-bottom: 30px;
            border-radius: 15px;
            font-weight: 500;
            text-align: center;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message.success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 2px solid #4caf50;
        }
        
        .message.error {
            background: #ffebee;
            color: #c62828;
            border: 2px solid #f44336;
        }
        
        .profile-section {
            background: white;
            padding: 40px;
            margin-bottom: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .profile-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: #333;
            margin-bottom: 25px;
        }
        
        .info-grid {
            display: grid;
            gap: 20px;
        }
        
        .info-item {
            padding: 25px;
            background: #f8f8f8;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }
        
        .info-item strong {
            color: #667eea;
            display: block;
            margin-bottom: 10px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-item span {
            color: #333;
            font-size: 1.3em;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <main>
        <div class="profile-container">
            <div class="page-header">
                <div class="page-subtitle">Account Settings</div>
                <h1 class="page-title">My Profile</h1>
                <p class="page-description">Manage your account information and preferences</p>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $messageType === 'success' ? '<i class="ri-checkbox-circle-line"></i>' : '<i class="ri-alert-line"></i>'; ?> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="profile-section">
                <h2>Account Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Username</strong>
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Role</strong>
                        <span><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Member Since</strong>
                        <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h2>Update Profile</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="e.g. 0700123456">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary"><i class="ri-save-line"></i> Save Changes</button>
                </form>
            </div>

            <div class="profile-section">
                <h2>Change Password</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary"><i class="ri-lock-line"></i> Change Password</button>
                </form>
            </div>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
