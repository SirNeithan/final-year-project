<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Dine - Your Favorite Food Delivered</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #fef8f5;
      color: #333;
      overflow-x: hidden;
    }

    /* Decorative circles */
    .circle {
      position: absolute;
      border-radius: 50%;
      opacity: 0.08;
      pointer-events: none;
    }

    .circle-1 {
      width: 300px;
      height: 300px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      top: 10%;
      left: 5%;
    }

    .circle-2 {
      width: 200px;
      height: 200px;
      background: transparent;
      border: 3px solid #f093fb;
      bottom: 15%;
      left: 10%;
    }

    .circle-3 {
      width: 150px;
      height: 150px;
      background: transparent;
      border: 3px solid #667eea;
      top: 20%;
      right: 15%;
    }

    .circle-4 {
      width: 100px;
      height: 100px;
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      bottom: 30%;
      right: 10%;
    }

    /* Header */
    .header {
      padding: 25px 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      z-index: 100;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 800;
      color: #667eea;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logo span {
      color: #333;
    }

    .nav-links {
      display: flex;
      gap: 40px;
      list-style: none;
      align-items: center;
    }

    .nav-links a {
      color: #333;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
      font-size: 0.95rem;
    }

    .nav-links a:hover {
      color: #667eea;
    }

    .join-btn {
      padding: 12px 30px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      transition: all 0.3s;
      box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .join-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
    }

    .cart-icon {
      position: relative;
      font-size: 1.5rem;
      cursor: pointer;
    }

    .cart-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #f093fb;
      color: white;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      font-weight: 700;
    }

    /* Hero Section */
    .hero {
      display: grid;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      padding: 60px 60px 100px;
      min-height: 85vh;
      position: relative;
    }

    .hero-left {
      z-index: 2;
      animation: fadeInLeft 0.8s ease-out;
    }

    @keyframes fadeInLeft {
      from {
        opacity: 0;
        transform: translateX(-50px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .hero h1 {
      font-size: 4.5rem;
      line-height: 1.2;
      margin-bottom: 25px;
      font-weight: 800;
      color: #1a1a1a;
    }

    .hero h1 .highlight {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero p {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #666;
      margin-bottom: 40px;
      max-width: 500px;
    }

    .hero-buttons {
      display: flex;
      gap: 20px;
      align-items: center;
      margin-bottom: 40px;
    }

    .btn-primary {
      padding: 18px 40px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1.05rem;
      transition: all 0.3s;
      box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
    }

    .hero-right {
      position: relative;
      height: 600px;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: fadeInRight 0.8s ease-out;
    }

    @keyframes fadeInRight {
      from {
        opacity: 0;
        transform: translateX(50px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .burger-container {
      position: relative;
      animation: float 3s ease-in-out infinite;
      width: 500px;
      height: 500px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      overflow: hidden;
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0px);
      }
      50% {
        transform: translateY(-20px);
      }
    }

    .burger-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .price-tag {
      position: absolute;
      bottom: 50px;
      right: 50px;
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      padding: 20px 30px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.05);
      }
    }

    .price-tag .label {
      font-size: 0.85rem;
      opacity: 0.9;
      margin-bottom: 5px;
    }

    .price-tag .price {
      font-size: 2rem;
      font-weight: 800;
    }

    .arrow-decoration {
      position: absolute;
      bottom: 80px;
      right: 180px;
      width: 100px;
      height: 100px;
    }

    .arrow-decoration svg {
      width: 100%;
      height: 100%;
      stroke: #f093fb;
      stroke-width: 2;
      fill: none;
    }

    /* Features Section */
    .features {
      padding: 100px 60px;
      background: white;
    }

    .section-header {
      text-align: center;
      max-width: 700px;
      margin: 0 auto 80px;
    }

    .section-header h2 {
      font-size: 3rem;
      margin-bottom: 20px;
      font-weight: 800;
      color: #1a1a1a;
    }

    .section-header h2 .highlight {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .section-header p {
      color: #666;
      font-size: 1.1rem;
      line-height: 1.8;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .feature-card {
      background: #fef8f5;
      padding: 40px 30px;
      border-radius: 20px;
      text-align: center;
      transition: all 0.4s;
      border: 2px solid transparent;
    }

    .feature-card:hover {
      transform: translateY(-10px);
      border-color: #667eea;
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
    }

    .feature-icon {
      font-size: 3.5rem;
      margin-bottom: 25px;
      display: block;
    }

    .feature-card h3 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .feature-card p {
      color: #666;
      line-height: 1.8;
    }

    /* Footer */
    footer {
      background: #1a1a1a;
      color: white;
      padding: 40px 60px;
      text-align: center;
    }

    footer p {
      opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .hero {
        grid-template-columns: 1fr;
        padding: 40px;
        text-align: center;
      }

      .hero-left {
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      .hero h1 {
        font-size: 3.5rem;
      }

      .hero p {
        max-width: 600px;
      }

      .hero-right {
        margin-top: 40px;
        height: 500px;
      }

      .burger-image {
        width: 350px;
        height: 350px;
      }

      .burger-container {
        width: 350px;
        height: 350px;
      }

      .features-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .header {
        padding: 20px 30px;
      }

      .nav-links {
        display: none;
      }

      .hero h1 {
        font-size: 2.5rem;
      }

      .hero-buttons {
        flex-direction: column;
        width: 100%;
      }

      .btn-primary {
        width: 100%;
        text-align: center;
      }

      .burger-image {
        width: 280px;
        height: 280px;
      }

      .burger-container {
        width: 280px;
        height: 280px;
      }

      .price-tag {
        bottom: 20px;
        right: 20px;
        padding: 15px 20px;
      }

      .price-tag .price {
        font-size: 1.5rem;
      }

      .section-header h2 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>

  <!-- Decorative circles -->
  <div class="circle circle-1"></div>
  <div class="circle circle-2"></div>
  <div class="circle circle-3"></div>
  <div class="circle circle-4"></div>

  <!-- Header -->
  <header class="header">
    <div class="logo">
      🍽️ <span>Smart</span>Dine
    </div>
    <nav>
      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#hot-item">Hot Item</a></li>
        <li><a href="#menus">Menus</a></li>
        <li><a href="#contact">Contact us</a></li>
        <li><a href="pages/auth/login.php" class="join-btn">Join</a></li>
        <li class="cart-icon">
          🛒
          <span class="cart-badge">0</span>
        </li>
      </ul>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="hero-left">
      <h1>
        Your Favorite<br>
        Food Delivered<br>
        <span class="highlight">Hot & Delicious</span>
      </h1>
      <p>
        Keep it easy with these simple but delicious recipes. 
        From make-ahead lunches and midweek meals to fuss-free sides.
      </p>
      <div class="hero-buttons">
        <a href="pages/auth/login.php" class="btn-primary">Get Started</a>
      </div>
    </div>

    <div class="hero-right">
      <div class="burger-container">
        <img src="assets/images/food pics/Cheeseburger.jpg" alt="Delicious Burger" class="burger-image">
        
        <!-- Arrow decoration -->
        <div class="arrow-decoration">
          <svg viewBox="0 0 100 100">
            <path d="M 10 50 Q 30 20, 60 40 T 90 50" stroke-dasharray="5,5">
              <animate attributeName="stroke-dashoffset" from="0" to="10" dur="1s" repeatCount="indefinite"/>
            </path>
            <polygon points="85,45 95,50 85,55" fill="#f093fb"/>
          </svg>
        </div>

        <!-- Price tag -->
        <div class="price-tag">
          <div class="label">Only</div>
          <div class="price">UGX 25K</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features" id="menus">
    <div class="section-header">
      <h2>Why Choose <span class="highlight">Smart Dine</span></h2>
      <p>
        We bring together the best restaurants in Uganda, ensuring every meal 
        is a memorable experience with quality and convenience.
      </p>
    </div>

    <div class="features-grid">
      <div class="feature-card">
        <span class="feature-icon">📱</span>
        <h3>Easy Orders</h3>
        <p>Browse menus and order with just a few taps. Simple, fast, and intuitive interface.</p>
      </div>

      <div class="feature-card">
        <span class="feature-icon">⚡</span>
        <h3>Fast Delivery</h3>
        <p>Quick delivery and no long waits. Your food arrives hot and fresh every time.</p>
      </div>

      <div class="feature-card">
        <span class="feature-icon">🍽️</span>
        <h3>Multi-Restaurant</h3>
        <p>Choose from multiple partner restaurants, each offering unique and delicious flavors.</p>
      </div>

      <div class="feature-card">
        <span class="feature-icon">💳</span>
        <h3>Secure Payment</h3>
        <p>Safe and cashless dining experience with our secure payment system.</p>
      </div>

      <div class="feature-card">
        <span class="feature-icon">⭐</span>
        <h3>Quality Food</h3>
        <p>Fresh ingredients and delicious meals prepared by expert chefs.</p>
      </div>

      <div class="feature-card">
        <span class="feature-icon">📦</span>
        <h3>Track Orders</h3>
        <p>Real-time order tracking and complete order history at your fingertips.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact">
    <p>📞 Contact us: 0766191751 | Smart Dine - Your Favorite Food Delivered 🍽️</p>
    <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.6;">© 2026 Smart Dine. All rights reserved.</p>
  </footer>

</body>
</html>
