<?php
session_start();
require 'includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "My Profile - Smart Dine";
$headerTitle = "👤 My Profile";

$userId = $_SESSION['user_id'];
$message = '';
$messageType = 'success';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email = trim($_POST['email']);
    
    try {
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $userId]);
        $message = 'Profile updated successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error updating profile: ' . $e->getMessage();
        $messageType = 'error';
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
} catch (Exception $e) {
    die('Error fetching user data: ' . $e->getMessage());
}

include 'includes/header.php';
?>

<style>
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .message {
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 15px;
        font-weight: 500;
        text-align: center;
    }
    
    .message.success {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        border: 2px solid rgba(102, 126, 234, 0.3);
    }
    
    .message.error {
        background: rgba(245, 87, 108, 0.1);
        color: #f5576c;
        border: 2px solid rgba(245, 87, 108, 0.3);
    }
    
    .profile-section {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        padding: 40px;
        margin-bottom: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .profile-section h2 {
        font-size: 1.8em;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 25px;
        font-weight: 700;
    }
    
    .info-grid {
        display: grid;
        gap: 20px;
    }
    
    .info-item {
        padding: 20px;
        background: rgba(102, 126, 234, 0.05);
        border-radius: 15px;
        border-left: 4px solid #667eea;
    }
    
    .info-item strong {
        color: #667eea;
        display: block;
        margin-bottom: 8px;
        font-size: 0.9em;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .info-item span {
        color: #333;
        font-size: 1.2em;
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #333;
    }
    
    .form-group input {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 15px;
        font-size: 1rem;
        transition: all 0.3s;
        font-family: 'Poppins', sans-serif;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .btn {
        padding: 15px 35px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 1.05em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
    }
    
    @media (max-width: 768px) {
        .profile-section {
            padding: 25px;
        }
    }
</style>

<div class="profile-container">
    <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $messageType === 'success' ? '✅' : '⚠️'; ?> <?php echo htmlspecialchars($message); ?>
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
        <h2>Update Email</h2>
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <button type="submit" name="update_profile" class="btn">💾 Update Email</button>
        </form>
    </div>

    <div class="profile-section">
        <h2>Change Password</h2>
        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="Enter current password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            </div>
            <button type="submit" name="change_password" class="btn">🔒 Change Password</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
