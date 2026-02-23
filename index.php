<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Dine - Your Digital Restaurant Experience</title>
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
      overflow-x: hidden;
    }

    .hero {
      text-align: center;
      color: white;
      padding: 80px 20px;
      max-width: 900px;
      margin: 0 auto;
    }

    .hero h1 {
      font-size: 4rem;
      margin-bottom: 20px;
      font-weight: 700;
      text-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .emoji-row {
      font-size: 3rem;
      margin-bottom: 30px;
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }

    .hero p {
      font-size: 1.4rem;
      margin-bottom: 40px;
      color: rgba(255,255,255,0.95);
      line-height: 1.8;
    }

    .cta-btn {
      padding: 18px 50px;
      font-size: 1.3rem;
      border: none;
      border-radius: 30px;
      background: rgba(255, 255, 255, 0.85);
      color: #667eea;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
      font-weight: 700;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .cta-btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.3);
      background: white;
    }

    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 20px;
    }

    .feature-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      transition: all 0.3s;
      text-align: center;
    }

    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .feature-card span {
      font-size: 3rem;
      display: block;
      margin-bottom: 20px;
    }

    .feature-card h3 {
      color: #667eea;
      margin-bottom: 15px;
      font-size: 1.5rem;
    }

    .feature-card p {
      color: #666;
      font-size: 1rem;
    }

    .floating {
      position: fixed;
      font-size: 3rem;
      opacity: 0.15;
      animation: drift 20s linear infinite;
      pointer-events: none;
    }

    @keyframes drift {
      from { transform: translateY(100vh) rotate(0deg); }
      to { transform: translateY(-100vh) rotate(360deg); }
    }

    .floating:nth-child(1) { left: 10%; animation-delay: 0s; }
    .floating:nth-child(2) { left: 30%; animation-delay: 5s; }
    .floating:nth-child(3) { left: 50%; animation-delay: 10s; }
    .floating:nth-child(4) { left: 70%; animation-delay: 15s; }
    .floating:nth-child(5) { left: 90%; animation-delay: 3s; }

    footer {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      color: #333;
      text-align: center;
      padding: 30px;
      margin-top: 80px;
      font-weight: 500;
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
      }

      .hero p {
        font-size: 1.1rem;
      }

      .features {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Floating emojis -->
  <div class="floating">🍕</div>
  <div class="floating">🍔</div>
  <div class="floating">🌮</div>
  <div class="floating">🍩</div>
  <div class="floating">🍜</div>

  <div class="hero">
    <h1>Smart Dine</h1>
    <div class="emoji-row">🍔 🍕 🍜 🥗 🍰</div>
    <p>
      Your all-in-one digital restaurant experience.<br>
      Order smarter. Dine better. Live tastier.
    </p>

    <a href="login.php" class="cta-btn">
      Get Started 🚀
    </a>

    <div class="features">
      <div class="feature-card">
        <span>📱</span>
        <h3>Easy Orders</h3>
        <p>Browse menus and order with just a few taps</p>
      </div>

      <div class="feature-card">
        <span>⏱️</span>
        <h3>Fast Service</h3>
        <p>Quick delivery and no long waits</p>
      </div>

      <div class="feature-card">
        <span>🍽️</span>
        <h3>Multi-Restaurant</h3>
        <p>Choose from multiple partner restaurants</p>
      </div>

      <div class="feature-card">
        <span>💳</span>
        <h3>Smart Pay</h3>
        <p>Secure and cashless dining experience</p>
      </div>

      <div class="feature-card">
        <span>⭐</span>
        <h3>Quality Food</h3>
        <p>Fresh ingredients and delicious meals</p>
      </div>

      <div class="feature-card">
        <span>📦</span>
        <h3>Track Orders</h3>
        <p>Real-time order tracking and history</p>
      </div>
    </div>
  </div>

  <footer>
    <p>📞 Contact us: +123-456-7890 | Smart Dine - Taste the Difference 🍽️</p>
  </footer>

</body>
</html>
