<?php
session_start();
include_once __DIR__ . "/includes/header.php";
?>

<!-- HERO BANNER -->
<section class="contact-hero">
  <div class="contact-hero__overlay">
    <div class="contact-hero__content">
      <p class="contact-hero__subtitle">We’re here to hear from you</p>
      <h1 class="contact-hero__title">CONTACT</h1>
      <p class="contact-hero__desc">Nice to hear from you</p>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="contact-page container">
  <div class="contact-grid">

    <!-- LEFT: GET IN TOUCH -->
    <div class="contact-card">
      <h3 class="contact-card__title">GET IN TOUCH</h3>
      <p class="contact-card__text">
        You can leave us a message and we will get back to you as soon as possible.
      </p>

      <form class="contact-form" action="" method="POST" autocomplete="on">
        <div class="contact-field">
          <label for="name">Name</label>
          <input id="name" type="text" name="name" placeholder="Your Name" required>
        </div>

        <div class="contact-field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" placeholder="Your Email" required>
        </div>

        <div class="contact-field">
          <label for="phone">Phone</label>
          <input id="phone" type="tel" name="phone" placeholder="Your Phone" required>
        </div>

        <div class="contact-field">
          <label for="subject">Subject</label>
          <input id="subject" type="text" name="subject" placeholder="Subject">
        </div>

        <div class="contact-field">
          <label for="message">Message</label>
          <textarea id="message" name="message" placeholder="Your Message..." rows="6" required></textarea>
        </div>

        <button type="submit" class="contact-btn">SEND MESSAGE</button>
      </form>
    </div>

    <!-- RIGHT: FIND US -->
    <div class="contact-card">
      <h3 class="contact-card__title">FIND US</h3>

      <div class="contact-info">
        <p>Address: 40 Park Ave, Brooklyn, New York 11250</p>
        <p>Phone: +1 (222) 333 444</p>
        <p>Email: contact@puckscoffee.com</p>
      </div>

      <div class="map-box">
        <!-- Replace the src with your real location embed -->
        <iframe
          title="Google Map"
          src="https://www.google.com/maps?q=Kuala%20Lumpur&output=embed"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>

  </div>

  <!-- BOTTOM INFO (3 columns) -->
  <div class="contact-bottom">
    <div class="bottom-box">
      <h4>ADDRESS</h4>
      <p>Pucks Coffee</p>
      <p>Setapak, Kuala Lumpur</p>
    </div>

    <div class="bottom-box">
      <h4>RESERVATION</h4>
      <p>+60 11-2222 3333</p>
      <p>contact@puckscoffee.com</p>
    </div>

    <div class="bottom-box">
      <h4>OPEN HOURS</h4>
      <p>Mon–Fri: 9AM – 10PM</p>
      <p>Sat–Sun: 9AM – 11PM</p>
    </div>
  </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
