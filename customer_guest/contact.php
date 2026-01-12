<?php
session_start();
include_once __DIR__ . "/includes/header.php";
?>

<!-- ================= CONTACT PAGE ================= -->
<section id="contact" class="contact container">
    <h2 class="section-title">Contact Us</h2>

    <div class="address-img">
        <img src="image/Address.png" alt="Address">
    </div>

    <form action="" method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="tel" name="phone" placeholder="Your Phone" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message..." required></textarea>
        <button type="submit" class="btn">Send Message</button>
    </form>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
