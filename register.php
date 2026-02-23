<?php
session_start();
require 'includes/connect.php';

$regError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $regUsername = trim($_POST['reg_username'] ?? '');
  $regEmail = trim($_POST['reg_email'] ?? '');
  $regPassword = $_POST['reg_password'] ?? '';

  if ($regUsername && $regEmail && $regPassword) {
    try {
      $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
      $check->execute([$regUsername, $regEmail]);

      if ($check->fetch()) {
        $regError = 'Username or email already exists';
      } else {
        $hashed = password_hash($regPassword, PASSWORD_BCRYPT);
        $insert = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $insert->execute([$regUsername, $hashed, $regEmail]);

        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['username'] = $regUsername;
        $_SESSION['role'] = 'user';
        header('Location: home.php', true, 302);
        exit();
      }
    } catch (Exception $e) {
      $regError = 'Registration error: ' . $e->getMessage();
    }
  } else {
    $regError = 'Please fill in all fields';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Account - Smart Dine</title>
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

    .register-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      width: 100%;
      max-width: 420px;
      padding: 50px 40px;
      border-radius: 25px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      text-align: center;
    }

    .register-card h1 {
      font-size: 2.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .register-card p {
      color: #666;
      margin-bottom: 30px;
      font-size: 1.1rem;
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

    .register-btn {
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

    .register-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
    }

    .divider {
      margin: 30px 0;
      border-top: 2px solid rgba(102, 126, 234, 0.1);
    }

    .login-link {
      color: #667eea;
      font-weight: 600;
      text-decoration: none;
      font-size: 1.05rem;
    }

    .login-link:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .register-card {
        padding: 40px 30px;
      }

      .register-card h1 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="register-card">
    <h1>Create Account</h1>
    <p>Join Smart Dine today! 🎉</p>

    <form method="POST" action="register.php">
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="reg_username" placeholder="Choose a username" required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="reg_email" placeholder="Enter your email" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="reg_password" placeholder="Create a password" required>
      </div>

      <?php if (!empty($regError)): ?>
        <div class="error-message">⚠️ <?php echo htmlspecialchars($regError); ?></div>
      <?php endif; ?>

      <button class="register-btn" type="submit">Create Account ✨</button>
    </form>

    <div class="divider"></div>

    <p style="color: #666; margin-bottom: 15px;">Already have an account?</p>
    <a class="login-link" href="login.php">Login here 🚀</a>
  </div>
</body>
</html>

