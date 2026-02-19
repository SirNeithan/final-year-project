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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    /* ...existing code... */
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
      font-family: 'Poppins', sans-serif;
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
  </style>
</head>
<body>
  <div class="login-card">
    <h1>Create Account</h1>
    <p>Join Smart Dine</p>

    <form method="POST" action="register.php">
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="reg_username" placeholder="Choose username" required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="reg_email" placeholder="Enter email" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="reg_password" placeholder="Create password" required>
      </div>

      <?php if (!empty($regError)): ?>
        <div style="color: #ff6f61; font-size: 0.9rem; margin-bottom: 1rem;"><?php echo $regError; ?></div>
      <?php endif; ?>

      <button class="login-btn" type="submit">Create Account ✨</button>
    </form>

    <div style="margin-top: 1rem;">
      <a href="login.php" style="color: #666; text-decoration: none;">Back to login</a>
    </div>
  </div>
</body>
</html>

