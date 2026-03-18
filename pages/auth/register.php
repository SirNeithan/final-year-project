<?php
session_start();
require '../../includes/connect.php';

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
        header('Location: ../../home.php', true, 302);
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - Smart Dine</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      min-height: 100vh;
      background: #ffffff;
      overflow-x: hidden;
    }

    .auth-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: 100vh;
      max-height: 100vh;
    }

    .auth-left {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px;
      background: #ffffff;
      overflow-y: auto;
    }

    .auth-form {
      width: 100%;
      max-width: 450px;
      animation: slideInLeft 0.6s ease-out;
    }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 40px;
      animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .logo-icon {
      font-size: 2.5rem;
    }

    .logo-text {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 700;
      color: #333;
    }

    .auth-form h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      color: #333;
      margin-bottom: 12px;
      font-weight: 700;
    }

    .auth-form .subtitle {
      color: #999;
      font-size: 1.05rem;
      margin-bottom: 40px;
    }

    .input-group {
      margin-bottom: 25px;
      animation: slideInLeft 0.6s ease-out;
      animation-fill-mode: both;
    }

    .input-group:nth-child(1) {
      animation-delay: 0.1s;
    }

    .input-group:nth-child(2) {
      animation-delay: 0.2s;
    }

    .input-group:nth-child(3) {
      animation-delay: 0.3s;
    }

    .input-group label {
      font-size: 0.95rem;
      color: #333;
      font-weight: 500;
      display: block;
      margin-bottom: 10px;
    }

    .input-group input {
      width: 100%;
      padding: 16px 20px;
      border-radius: 12px;
      border: 2px solid #e8e8e8;
      outline: none;
      transition: all 0.3s;
      font-size: 1rem;
      background: #fafafa;
    }

    .input-group input:focus {
      border-color: #667eea;
      background: white;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .error-message {
      color: #c62828;
      font-size: 0.9rem;
      margin-bottom: 20px;
      padding: 14px 18px;
      background: #ffebee;
      border-radius: 12px;
      border-left: 4px solid #f44336;
    }

    .register-btn {
      width: 100%;
      padding: 18px;
      margin-top: 10px;
      border: none;
      border-radius: 50px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
      animation: slideInLeft 0.6s ease-out 0.4s;
      animation-fill-mode: both;
    }

    .register-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
    }

    .divider {
      margin: 35px 0;
      text-align: center;
      position: relative;
      animation: fadeIn 0.6s ease-out 0.5s;
      animation-fill-mode: both;
    }

    .divider::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: #e8e8e8;
    }

    .divider span {
      background: white;
      padding: 0 15px;
      position: relative;
      color: #999;
      font-size: 0.9rem;
    }

    .login-link {
      text-align: center;
      color: #666;
      font-size: 1rem;
      animation: fadeIn 0.6s ease-out 0.6s;
      animation-fill-mode: both;
    }

    .login-link a {
      color: #667eea;
      font-weight: 600;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    .back-home {
      margin-top: 25px;
      text-align: center;
      animation: fadeIn 0.6s ease-out 0.7s;
      animation-fill-mode: both;
    }

    .back-home a {
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .back-home a:hover {
      text-decoration: underline;
    }

    .auth-right {
      position: relative;
      background: url('../../assets/images/food pics/Caesar-Salad.jpg') center/cover;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding: 60px;
      animation: fadeIn 0.8s ease-out;
    }

    .auth-right-content {
      text-align: center;
      color: white;
      max-width: 500px;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
      padding: 40px;
      border-radius: 20px;
      animation: slideInUp 0.8s ease-out 0.3s;
      animation-fill-mode: both;
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .auth-right-content h2 {
      font-family: 'Playfair Display', serif;
      font-size: 3rem;
      margin-bottom: 20px;
      font-weight: 700;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    }

    .auth-right-content p {
      font-size: 1.2rem;
      line-height: 1.8;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
    }

    .features-list {
      margin-top: 40px;
      text-align: left;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
      font-size: 1.05rem;
      animation: slideInUp 0.6s ease-out;
      animation-fill-mode: both;
    }

    .feature-item:nth-child(1) {
      animation-delay: 0.5s;
    }

    .feature-item:nth-child(2) {
      animation-delay: 0.6s;
    }

    .feature-item:nth-child(3) {
      animation-delay: 0.7s;
    }

    .feature-icon {
      font-size: 1.8rem;
    }

    @media (max-width: 968px) {
      .auth-container {
        grid-template-columns: 1fr;
      }

      .auth-right {
        display: none;
      }

      .auth-left {
        padding: 40px 20px;
      }
    }

    @media (max-width: 480px) {
      .auth-form h1 {
        font-size: 2rem;
      }

      .logo-text {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="auth-container">
    <div class="auth-left">
      <div class="auth-form">
        <div class="logo">
          <span class="logo-icon"><i class="ri-restaurant-line"></i></span>
          <span class="logo-text">Smart Dine</span>
        </div>

        <h1>Create Account</h1>
        <p class="subtitle">Join Smart Dine today and start ordering! 🎉</p>

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
            <div class="error-message"><i class="ri-alert-line"></i> <?php echo htmlspecialchars($regError); ?></div>
          <?php endif; ?>

          <button class="register-btn" type="submit">Create Account</button>
        </form>

        <div class="divider">
          <span>or</span>
        </div>

        <p class="login-link">
          Already have an account? <a href="login.php">Login here</a>
        </p>

        <div class="back-home">
          <a href="../../index.php">← Back to Home</a>
        </div>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-right-content">
        <h2>Start Your Journey</h2>
        <p>Join thousands of food lovers enjoying delicious meals from the best restaurants in Uganda</p>
        
        <div class="features-list">
          <div class="feature-item">
            <span class="feature-icon"><i class="ri-user-add-line"></i></span>
            <span>Easy account setup</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon"><i class="ri-gift-line"></i></span>
            <span>Exclusive deals & offers</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon"><i class="ri-smartphone-line"></i></span>
            <span>Order tracking & history</span>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
