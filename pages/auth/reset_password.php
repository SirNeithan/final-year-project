<?php
session_start();
require '../../includes/connect.php';

$token = $_GET['token'] ?? '';
$message = '';
$messageType = '';
$valid = false;

// Validate token
if ($token && isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token) {
    if (strtotime($_SESSION['reset_expires']) > time()) {
        $valid = true;
    } else {
        $message = 'This reset link has expired. Please request a new one.';
        $messageType = 'error';
    }
} else {
    $message = 'Invalid or expired reset link.';
    $messageType = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($newPassword) < 6) {
        $message = 'Password must be at least 6 characters.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        try {
            $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $_SESSION['reset_user_id']]);

            // Clear reset session data
            unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_expires']);

            $message = 'Password reset successfully! <a href="login.php" style="color:#667eea;font-weight:600;">Login now</a>';
            $messageType = 'success';
            $valid = false;
        } catch (Exception $e) {
            $message = 'Error resetting password: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - SmartDine Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
    body { min-height:100vh; background:#f8f8ff; display:flex; align-items:center; justify-content:center; padding:20px; }
    .card { background:white; border-radius:20px; padding:50px 40px; max-width:460px; width:100%; box-shadow:0 10px 40px rgba(0,0,0,0.1); }
    .logo { font-size:2.5rem; text-align:center; margin-bottom:10px; }
    h1 { font-family:'Playfair Display',serif; font-size:2rem; color:#333; text-align:center; margin-bottom:8px; }
    .subtitle { color:#999; text-align:center; margin-bottom:35px; font-size:0.95rem; }
    .form-group { margin-bottom:20px; }
    label { display:block; font-weight:500; margin-bottom:8px; color:#333; }
    input { width:100%; padding:15px 20px; border:2px solid #e8e8e8; border-radius:12px; font-size:1rem; transition:all 0.3s; background:#fafafa; }
    input:focus { outline:none; border-color:#667eea; background:white; box-shadow:0 0 0 4px rgba(102,126,234,0.1); }
    .btn { width:100%; padding:16px; border:none; border-radius:50px; background:linear-gradient(135deg,#667eea,#764ba2); color:white; font-size:1.05rem; font-weight:600; cursor:pointer; transition:all 0.3s; margin-top:5px; }
    .btn:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(102,126,234,0.4); }
    .message { padding:15px 20px; border-radius:12px; margin-bottom:20px; font-size:0.95rem; }
    .message.success { background:#e8f5e9; color:#2e7d32; border-left:4px solid #4caf50; }
    .message.error { background:#ffebee; color:#c62828; border-left:4px solid #f44336; }
    .links { text-align:center; margin-top:25px; }
    .links a { color:#667eea; text-decoration:none; font-weight:500; font-size:0.95rem; }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo">🔒</div>
    <h1>Reset Password</h1>
    <p class="subtitle">Choose a new password for your account</p>

    <?php if ($message): ?>
      <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($valid): ?>
    <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" placeholder="At least 6 characters" required minlength="6">
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" placeholder="Repeat your new password" required>
      </div>
      <button type="submit" class="btn">Reset Password</button>
    </form>
    <?php endif; ?>

    <div class="links">
      <a href="login.php">← Back to Login</a>
    </div>
  </div>
</body>
</html>
