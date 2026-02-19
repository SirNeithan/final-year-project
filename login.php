<?php
session_start();
require 'includes/connect.php';

// Handle registration submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
  $regUsername = trim($_POST['reg_username'] ?? '');
  $regEmail = trim($_POST['reg_email'] ?? '');
  $regPassword = $_POST['reg_password'] ?? '';
  $regError = '';

  if ($regUsername && $regEmail && $regPassword) {
    try {
      // Check if username/email already exists
      $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
      $check->execute([$regUsername, $regEmail]);

      if ($check->fetch()) {
        $regError = 'Username or email already exists';
      } else {
        $hashed = password_hash($regPassword, PASSWORD_BCRYPT);
        $insert = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $insert->execute([$regUsername, $hashed, $regEmail]);

        // Auto-login after registration
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
    $regError = 'Please fill in all registration fields';
  }
}

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $error = '';
  
  if (!empty($username) && !empty($password)) {
    try {
      // Query the database for the user
      $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
      $stmt->execute([$username]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      
      // Verify password
      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        // Make sure no output before redirect
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
  <title>Smart Dine Place 🍔🍕</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #ff9a9e, #fad0c4);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background: white;
      width: 350px;
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
      text-align: center;
    }

    .login-card h1 {
      font-size: 1.8rem;
      margin-bottom: 0.5rem;
    }

    .emoji-row {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .login-card p {
      color: #666;
      margin-bottom: 1.5rem;
    }

    .input-group {
      margin-bottom: 1rem;
      text-align: left;
    }

    .input-group label {
      font-size: 0.9rem;
      color: #444;
    }

    .input-group input {
      width: 100%;
      padding: 0.7rem;
      margin-top: 0.3rem;
      border-radius: 10px;
      border: 1px solid #ddd;
      outline: none;
      transition: 0.3s;
    }

    .input-group input:focus {
      border-color: #ff6f61;
      box-shadow: 0 0 5px #ff6f61;
    }

    .login-btn {
      width: 100%;
      padding: 0.8rem;
      margin-top: 1rem;
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, #ff6f61, #ff9472);
      color: white;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .footer-text {
      margin-top: 1.5rem;
      font-size: 0.8rem;
      color: #999;
    }

    .divider {
      margin: 1.5rem 0;
      border-top: 1px solid #ddd;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h1>Smart Dine Place</h1>
    <div class="emoji-row">🍔 🍕 🍟 🌮 🍩</div>
    <p>Welcome back, foodie!</p>

    <form method="POST" action="login.php">
      <input type="hidden" name="login" value="1">
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>
      </div>

      <?php if (isset($error) && !empty($error)): ?>
        <div style="color: #ff6f61; font-size: 0.9rem; margin-bottom: 1rem;"><?php echo $error; ?></div>
      <?php endif; ?>

      <button class="login-btn" type="submit">Login 🚀</button>
    </form>

    <div class="divider"></div>

    <p style="color: #666; margin-bottom: 1rem;">New here?</p>
    <a class="login-btn" href="register.php" style="text-decoration: none; display: inline-block; text-align: center;">
      Create Account ✨
    </a>

    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #ddd;">
      <p style="color: #666; font-size: 0.85rem; margin-bottom: 0.8rem;"><strong>Demo Credentials:</strong></p>
      <p style="color: #999; font-size: 0.8rem; line-height: 1.6;">
        <strong>Users:</strong> demo1-demo4 / password<br>
        <strong>Admin:</strong> admin / admin123
      </p>
    </div>

    <div class="footer-text">
      © 2026 Smart Dine Place 🥗
    </div>
  </div>

</body>
</html>

