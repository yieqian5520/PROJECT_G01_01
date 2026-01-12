<?php
session_start();
include_once __DIR__ . "/includes/header.php";
?>

<section class="about-hero">
    <div class="about-hero-overlay">
        <div class="about-hero-content">
            <h1>About Pucks Coffee</h1>
            <p>Crafted with passion. Served with love.</p>
        </div>
    </div>
</section>

<section class="about-section container">
    <div class="about-grid">
        <div class="about-text">
            <h2>Our Story</h2>
            <p>
                At <strong>Pucks Coffee</strong>, we believe coffee is more than just a drink — it’s an experience.
                From carefully sourced beans to expert brewing techniques, every cup is made with passion.
            </p>

            <p>
                Our café is designed to be your second home — a place to relax, work, and connect.
                We aim to create unforgettable moments with every visit.
            </p>

            <a href="contact.php" class="btn">Visit Us</a>
        </div>

        <div class="about-image">
            <img src="image/About.png" alt="About Pucks Coffee">
        </div>
    </div>
</section>

<section class="about-values">
    <div class="container values-grid">
        <div class="value-box">
            <h3>Quality</h3>
            <p>Only the finest beans, roasted to perfection.</p>
        </div>
        <div class="value-box">
            <h3>Comfort</h3>
            <p>A cozy space made for everyone.</p>
        </div>
        <div class="value-box">
            <h3>Community</h3>
            <p>Bringing people together over great coffee.</p>
        </div>
    </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
