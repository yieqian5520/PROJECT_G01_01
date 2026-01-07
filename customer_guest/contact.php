<?php
session_start();
// Home Page
include_once __DIR__ . "/includes/header.php";
?>

<section id="contact" class="contact container">

    <h2 class="section-title">Contact Us</h2>

    <div class="row">

    <form action="" method="POST">
        <h3>Tell Us Something</h3>
        <input type="text" placeholder="Your Name" required>
        <input type="phone" placeholder="Your Phone" required>
        <input type="email" placeholder="Your Email" required>
        <textarea placeholder="Your Message..." required></textarea>
        <button type="submit" class="btn">Send Message</button>
    </form>
</div>
</section>



<?php
include_once __DIR__ . "/includes/footer.php";
?>