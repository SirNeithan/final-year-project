<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Smart Dine Place 🍽️</title>
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
      background: linear-gradient(135deg, #ffecd2, #fcb69f);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .hero {
      text-align: center;
      color: #333;
      padding: 3rem;
      max-width: 700px;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    .emoji-row {
      font-size: 2rem;
      margin-bottom: 1rem;
      animation: float 3s ease-in-out infinite;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      color: #555;
    }

    .cta-btn {
      padding: 1rem 2.5rem;
      font-size: 1.2rem;
      border: none;
      border-radius: 50px;
      background: linear-gradient(135deg, #ff6f61, #ff9472);
      color: white;
      cursor: pointer;
      transition: 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .cta-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .features {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-top: 3rem;
      flex-wrap: wrap;
    }

    .feature-card {
      background: white;
      padding: 1.5rem;
      border-radius: 20px;
      width: 180px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      transition: 0.3s;
    }

    .feature-card:hover {
      transform: translateY(-8px);
    }

    .feature-card span {
      font-size: 2rem;
    }

    .feature-card h3 {
      margin: 0.5rem 0;
    }

    @keyframes float {
      0% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0); }
    }

    /* Floating background food */
    .floating {
      position: absolute;
      font-size: 2rem;
      opacity: 0.2;
      animation: drift 15s linear infinite;
    }

    @keyframes drift {
      from { transform: translateY(100vh); }
      to { transform: translateY(-100vh); }
    }
  </style>
</head

>
<body>

  <!-- Floating emojis -->
  <div class="floating" style="left:10%">🍕</div>
  <div class="floating" style="left:30%">🍔</div>
  <div class="floating" style="left:60%">🌮</div>
  <div class="floating" style="left:80%">🍩</div>

  <div class="hero">
    <h1>Smart Dine Place</h1>
    <div class="emoji-row">🍔 🍕 🍜 🥗 🍰</div>
    <p>
      Your all-in-one digital restaurant experience.  
      Order smarter. Dine better. Live tastier.
    </p>

    <!-- This goes to your login page -->
    <a href="login.php" class="cta-btn">
      Enter the Kitchen 🚪🍳
    </a>

    <div class="features">
      <div class="feature-card">
        <span>📱</span>
        <h3>Easy Orders</h3>
        <p>One tap meals</p>
      </div>

      <div class="feature-card">
        <span>⏱️</span>
        <h3>Fast Service</h3>
        <p>No long waits</p>
      </div>

      <div class="feature-card">
        <span>💳</span>
        <h3>Smart Pay</h3>
        <p>Cashless dining</p>
      </div>
    </div>
  </div>

</body>
</html>

