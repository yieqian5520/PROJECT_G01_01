<?php
session_start();
include_once __DIR__ . "/includes/header.php";
?>

<!-- HERO BANNER -->
<section class="contact-hero">
  <div class="contact-hero__overlay">
    <div class="contact-hero__content">
      <h1 class="contact-hero__title">Visit Pucks Coffee</h1>
      <p class="contact-hero__desc">We'd love to welcome you for a cup of happiness ☕</p>
    </div>
  </div>
</section>

<!-- CONTACT INFO -->
<section class="contact-page container">
  <div class="contact-grid">

    <div class="contact-card">
      <div class="contact-icon">📍</div>
      <h3>Our Location</h3>
      <p>
        Back Portion, 180, Jalan Tun H S Lee,<br>
        Kuala Lumpur City Centre,<br>
        50000 Kuala Lumpur
      </p>
    </div>

    <div class="contact-card">
      <div class="contact-icon">📞</div>
      <h3>Call Us</h3>
      <p>012-253 0132</p>
      <p>We’re happy to assist you!</p>
    </div>

    <div class="contact-card">
      <div class="contact-icon">✉️</div>
      <h3>Email Us</h3>
      <p>puckscoffee@gmail.com</p>
    </div>

    <div class="contact-card">
      <div class="contact-icon">⏰</div>
      <h3>Opening Hours</h3>
      <p>Monday – Sunday: 12:00 PM – 12:00 AM</p>
      <p>Tuesday - Close</p>
    </div>

  </div>
</section>

<section style="padding:60px;">
  <iframe 
    src="https://maps.google.com/maps?q=Back%20Portion%2C%20180%20Jalan%20Tun%20H%20S%20Lee%2C%2050000%20Kuala%20Lumpur&t=&z=17&ie=UTF8&iwloc=&output=embed"
    width="100%" 
    height="400" 
    style="border:0;"
    loading="lazy">
  </iframe>
</section>


<!-- SOCIAL -->
<section class="social-section">
  <div class="container text-center">
    <h2 class="section-title">Stay Connected</h2>

    <div class="social-icons">

      <a href="https://www.facebook.com/profile.php?id=61556307396229"
         target="_blank"
         class="social-icon facebook">
        <i class="bi bi-facebook"></i>
      </a>

      <a href="https://www.instagram.com/pucksandfriends/"
         target="_blank"
         class="social-icon instagram">
        <i class="bi bi-instagram"></i>
      </a>

    </div>
  </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
