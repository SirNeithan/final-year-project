<?php
session_start();
require 'includes/connect.php';

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $error = '';
  
  if (!empty($username) && !empty($password)) {
    try {
      $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
      $stmt->execute([$username]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: home.php', true, 302);
        exit();
      } else {
        $error = $user ? 'Invalid password' : 'User not found';
      }
    } catch (Exception $e) {
      $error = 'Login error: ' . $e->getMessage();
    }
  } else {
    $error = 'Please enter both username and password';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Smart Dine</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      width: 100%;
      max-width: 420px;
      padding: 50px 40px;
      border-radius: 25px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      text-align: center;
    }

    .login-card h1 {
      font-size: 2.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .emoji-row {
      font-size: 2rem;
      margin-bottom: 30px;
    }

    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .input-group label {
      font-size: 0.95rem;
      color: #333;
      font-weight: 500;
      display: block;
      margin-bottom: 8px;
    }

    .input-group input {
      width: 100%;
      padding: 15px 20px;
      border-radius: 15px;
      border: 2px solid rgba(102, 126, 234, 0.2);
      outline: none;
      transition: all 0.3s;
      font-size: 1rem;
    }

    .input-group input:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .error-message {
      color: #f5576c;
      font-size: 0.9rem;
      margin-bottom: 20px;
      padding: 12px;
      background: rgba(245, 87, 108, 0.1);
      border-radius: 10px;
    }

    .login-btn {
      width: 100%;
      padding: 15px;
      margin-top: 10px;
      border: none;
      border-radius: 15px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .login-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
    }

    .divider {
      margin: 30px 0;
      border-top: 2px solid rgba(102, 126, 234, 0.1);
    }

    .register-link {
      color: #667eea;
      font-weight: 600;
      text-decoration: none;
      font-size: 1.05rem;
    }

    .register-link:hover {
      text-decoration: underline;
    }

    .demo-credentials {
      margin-top: 30px;
      padding-top: 30px;
      border-top: 2px solid rgba(102, 126, 234, 0.1);
    }

    .demo-credentials p {
      color: #666;
      font-size: 0.9rem;
      line-height: 1.8;
    }

    .demo-credentials strong {
      color: #667eea;
    }

    .back-home {
      margin-top: 20px;
    }

    .back-home a {
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
    }

    .back-home a:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .login-card {
        padding: 40px 30px;
      }

      .login-card h1 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h1>Smart Dine</h1>
    <div class="emoji-row">🍔 🍕 🍜 🥗 🍰</div>

    <form method="POST" action="login.php">
      <input type="hidden" name="login" value="1">
      
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter your username" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>

      <?php if (isset($error) && !empty($error)): ?>
        <div class="error-message">⚠️ <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <button class="login-btn" type="submit">Login 🚀</button>
    </form>

    <div class="divider"></div>

    <p style="color: #666; margin-bottom: 15px;">Don't have an account?</p>
    <a class="register-link" href="register.php">Create Account ✨</a>

    <div class="demo-credentials">
      <p><strong>Demo Credentials:</strong></p>
      <p>
        <strong>Users:</strong> demo1-demo4 / password<br>
        <strong>Admin:</strong> admin / admin123
      </p>
    </div>

    <div class="back-home">
      <a href="index.php">← Back to Home</a>
    </div>
  </div>

</body>
</html>
