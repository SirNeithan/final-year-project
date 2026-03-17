<?php
session_start();
require '../../includes/connect.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $message = 'Please enter your email address.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate a reset token and store it
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Store token in session (simple approach — no extra table needed)
                $_SESSION['reset_token'] = $token;
                $_SESSION['reset_user_id'] = $user['id'];
                $_SESSION['reset_expires'] = $expires;

                // In a real system you'd email the link. Here we show it directly.
                $resetLink = "reset_password.php?token=" . $token;
                $message = "A reset link has been generated. <a href='$resetLink' style='color:#667eea;font-weight:600;'>Click here to reset your password</a>.";
                $messageType = 'success';
            } else {
                // Don't reveal whether email exists
                $message = 'If that email is registered, a reset link has been sent.';
                $messageType = 'success';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
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
  <title>Forgot Password - SmartDine Hub</title>
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
    .links a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo">🔑</div>
    <h1>Forgot Password</h1>
    <p class="subtitle">Enter your email and we'll send you a reset link</p>

    <?php if ($message): ?>
      <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your registered email" required>
      </div>
      <button type="submit" class="btn">Send Reset Link</button>
    </form>

    <div class="links">
      <a href="login.php">← Back to Login</a>
    </div>
  </div>
</body>
</html>
