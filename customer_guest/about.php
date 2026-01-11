<?php
session_start();
// Home Page
include_once __DIR__ . "/includes/header.php";
?>

<section id="about" class="about container">
    <div class="about-text">
        <h2 class="section-title">About Us</h2>
        <p>
            At <strong>Pucks Coffee</strong>, we believe in serving coffee
            brewed to perfection using the finest beans from around the world.
            Our café provides a cozy environment to relax, work, or meet friends.
        </p>
        <a href="#contact" class="btn secondary-btn">Visit Us</a>
    </div>

    <div class="about-img">
        <img src="image/About.png" alt="">
    </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>