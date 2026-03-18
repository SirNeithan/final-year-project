<?php
session_start();
$pageTitle = "About Us - SmartDine Hub";
$headerTitle = "SmartDine Hub";
include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/elegant-theme.css">
<style>
.about-wrap { max-width:1100px; margin:0 auto; padding:40px 20px; }
.hero-banner {
    background: linear-gradient(135deg,rgba(102,126,234,0.12),rgba(118,75,162,0.12));
    border-radius:20px; padding:60px 40px; text-align:center; margin-bottom:60px;
}
.hero-banner h1 { font-family:'Playfair Display',serif; font-size:3em; color:#333; margin-bottom:15px; }
.hero-banner p { font-size:1.15em; color:#666; max-width:650px; margin:0 auto; line-height:1.8; }
.section { margin-bottom:60px; }
.section h2 { font-family:'Playfair Display',serif; font-size:2em; color:#333; margin-bottom:20px; }
.values-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:25px; }
.value-card { background:white; border-radius:16px; padding:30px 25px; box-shadow:0 5px 20px rgba(0,0,0,0.07); text-align:center; border:1px solid #f0f0f0; }
.value-card .icon { font-size:2.5em; margin-bottom:15px; }
.value-card h3 { font-size:1.15em; color:#333; margin-bottom:10px; font-weight:600; }
.value-card p { color:#777; font-size:0.92em; line-height:1.7; }
.contact-grid { display:grid; grid-template-columns:1fr 1fr; gap:40px; }
.contact-info p { color:#555; line-height:2; font-size:1.05em; }
.contact-form .form-group { margin-bottom:18px; }
.contact-form label { display:block; font-weight:500; margin-bottom:6px; color:#333; }
.contact-form input, .contact-form textarea {
    width:100%; padding:14px 18px; border:2px solid #e8e8e8; border-radius:12px;
    font-family:'Poppins',sans-serif; font-size:0.95em; transition:all 0.3s; background:#fafafa;
}
.contact-form input:focus, .contact-form textarea:focus {
    outline:none; border-color:#667eea; background:white; box-shadow:0 0 0 4px rgba(102,126,234,0.1);
}
.contact-form textarea { resize:vertical; min-height:120px; }
.submit-btn {
    padding:14px 40px; border:none; border-radius:50px;
    background:linear-gradient(135deg,#667eea,#764ba2); color:white;
    font-weight:600; font-size:1em; cursor:pointer; transition:all 0.3s;
}
.submit-btn:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(102,126,234,0.4); }
.success-msg { background:#e8f5e9; color:#2e7d32; padding:14px 18px; border-radius:12px; border-left:4px solid #4caf50; margin-bottom:20px; }
@media(max-width:768px){ .contact-grid{ grid-template-columns:1fr; } .hero-banner{ padding:40px 20px; } }
</style>

<?php
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    // In a real system you'd send an email here
    $sent = true;
}
?>

<div class="about-wrap">
    <div class="hero-banner">
        <h1><i class="ri-restaurant-line"></i> About SmartDine Hub</h1>
        <p>We connect food lovers across Uganda with the best restaurants in their region — making every meal an experience worth remembering.</p>
    </div>

    <div class="section">
        <h2>Our Story</h2>
        <p style="color:#555; line-height:1.9; font-size:1.05em;">
            SmartDine Hub was born from a simple idea: great food should be easy to find, no matter where you are in Uganda.
            We started by partnering with a handful of restaurants in Kampala and have since grown into a multi-region platform
            connecting diners with restaurants across Central, Eastern, Western and Northern Uganda.
            Our mission is to make ordering food as enjoyable as eating it.
        </p>
    </div>

    <div class="section">
        <h2>Our Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="icon"><i class="ri-shake-hands-line"></i></div>
                <h3>Partnership</h3>
                <p>We work closely with every restaurant to ensure quality and consistency on every order.</p>
            </div>
            <div class="value-card">
                <div class="icon"><i class="ri-flashlight-line"></i></div>
                <h3>Speed</h3>
                <p>Fast ordering, fast delivery. We respect your time and your hunger.</p>
            </div>
            <div class="value-card">
                <div class="icon"><i class="ri-map-line"></i></div>
                <h3>Community</h3>
                <p>Supporting local restaurants and creating jobs across Uganda's regions.</p>
            </div>
            <div class="value-card">
                <div class="icon"><i class="ri-lock-line"></i></div>
                <h3>Trust</h3>
                <p>Secure payments, honest pricing and transparent order tracking every time.</p>
            </div>
        </div>
    </div>

    <div class="section" id="contact">
        <h2>Contact Us</h2>
        <div class="contact-grid">
            <div class="contact-info">
                <p><i class="ri-phone-line"></i> <strong>Phone:</strong> 0766191751</p>
                <p><i class="ri-mail-line"></i> <strong>Email:</strong> hello@smartdinehub.ug</p>
                <p><i class="ri-map-pin-line"></i> <strong>Address:</strong> Kampala, Uganda</p>
                <p><i class="ri-time-line"></i> <strong>Hours:</strong> Mon–Sun, 8am – 10pm</p>
                <br>
                <p style="color:#667eea; font-weight:600;">We typically respond within 2 hours.</p>
            </div>
            <div class="contact-form">
                <?php if ($sent): ?>
                    <div class="success-msg"><i class="ri-checkbox-circle-line"></i> Message sent! We'll get back to you shortly.</div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="you@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" placeholder="How can we help?" required></textarea>
                    </div>
                    <button type="submit" name="contact_submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
