<?php
session_start();
include_once __DIR__ . "/includes/header.php";
?>

<!-- ================= ABOUT HERO ================= -->
<section class="about-hero">
  <div class="about-hero-overlay">
    <div class="about-hero-content">
      <h1>About Pucks Coffee</h1>
      <p>Crafted with passion. Served with love.</p>
    </div>
  </div>
</section>

<!-- ================= ABOUT MAIN ================= -->
<section class="about-section container">
  <div class="about-grid">
    <div class="about-text">
      <h2>Our Story</h2>
      <p>
        At Pucks Coffee, we believe coffee is more than a drink — it’s an experience.
        From carefully sourced beans to expert brewing, every cup is made with passion.
      </p>
      <p>
        We created a cozy space for you to relax, study, work, and meet friends.
        Our goal is simple: serve quality coffee, warm hospitality, and good vibes every day.
      </p>

      <div class="about-badges">
        <span class="badge">Premium Beans</span>
        <span class="badge">Cozy Space</span>
        <span class="badge">Fresh Daily</span>
      </div>

      <div class="about-actions">
        <a href="menu.php" class="btn">View Menu</a>
        <a href="contact.php" class="btn secondary-btn">Visit Us</a>
      </div>
    </div>

    <div class="about-image">
      <img src="image/About.png" alt="About Pucks Coffee">
    </div>
  </div>
</section>

<!-- ================= STATS ================= -->
<section class="about-stats">
  <div class="container stats-grid">
    <div class="stat-box">
      <h3>10+</h3>
      <p>Signature Drinks</p>
    </div>
    <div class="stat-box">
      <h3>100%</h3>
      <p>Fresh Ingredients</p>
    </div>
    <div class="stat-box">
      <h3>5★</h3>
      <p>Customer Satisfaction</p>
    </div>
  </div>
</section>

<!-- ================= VALUES ================= -->
<section class="about-values">
  <div class="container values-grid">
    <div class="value-box">
      <h3>Quality</h3>
      <p>We use carefully selected beans and consistent brewing to deliver rich flavour.</p>
    </div>
    <div class="value-box">
      <h3>Comfort</h3>
      <p>A peaceful café environment where you can relax, work, and feel at home.</p>
    </div>
    <div class="value-box">
      <h3>Community</h3>
      <p>We love building connections and creating moments over coffee and desserts.</p>
    </div>
  </div>
</section>

<!-- ================= WHY CHOOSE US ================= -->
<section class="about-why container">
  <h2 class="section-title">Why Choose Us</h2>

  <div class="why-grid">
    <div class="why-card">
      <h3>Handcrafted Barista</h3>
      <p>Every cup is made by trained baristas with consistent taste and care.</p>
    </div>
    <div class="why-card">
      <h3>Fresh Menu Daily</h3>
      <p>We keep it fresh with daily-prepared ingredients and clean serving standards.</p>
    </div>
    <div class="why-card">
      <h3>Perfect for Work & Chill</h3>
      <p>Comfortable seating and calm vibes for study, meetings, and hangouts.</p>
    </div>
    <div class="why-card">
      <h3>Friendly Service</h3>
      <p>Warm hospitality is our signature — we treat every customer like family.</p>
    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<section class="about-cta">
  <div class="container cta-box">
    <h2>Come and enjoy your coffee moment</h2>
    <p>Visit our café today or send us a message for enquiries and reservations.</p>
    <a href="contact.php" class="btn">Contact Us</a>
  </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>