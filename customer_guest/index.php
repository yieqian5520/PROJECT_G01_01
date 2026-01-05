<?php
// Home Page
include_once __DIR__ . "/includes/header.php";
?>

<!-- ================= HERO SECTION ================= -->
<section id="home" class="hero">
    <div class="hero-overlay">
        <div class="hero-text">
            <h1>Freshly Brewed Coffee, Just for You</h1>
            <p>
                Experience the perfect cup every time at
                <span>Pucks Coffee</span>
            </p>
            <a href="#menu" class="btn">View Men    u</a>
        </div>
    </div>
</section>

<!-- ================= MENU SECTION ================= -->
<section id="menu" class="menu container">
    <h2 class="section-title">Our Menu</h2>

    <div class="menu-grid">
        <div class="menu-item">
            <img src="image/espresso.jpg" alt="Espresso">
            <h3>Espresso</h3>
            <p>Strong and bold coffee shot</p>
            <span>RM 8.00</span>
        </div>

        <div class="menu-item">
            <img src="image/latte.jpg" alt="Latte">
            <h3>Latte</h3>
            <p>Smooth espresso with steamed milk</p>
            <span>RM 12.00</span>
        </div>

        <div class="menu-item">
            <img src="image/cappuccino.jpg" alt="Cappuccino">
            <h3>Cappuccino</h3>
            <p>Perfect blend of espresso, milk & foam</p>
            <span>RM 11.00</span>
        </div>
    </div>
</section>

<!-- ================= ABOUT SECTION ================= -->
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
        <img src="image/about.webp" alt="About Pucks Coffee">
    </div>
</section>

<!-- ================= OFFERS SECTION ================= -->
<section id="offers" class="offers">
    <div class="container">
        <h2 class="section-title">Special Offers</h2>

        <div class="offers-grid">
            <div class="offer-card">
                <h3>Buy 1 Get 1 Free</h3>
                <p>Available every Monday</p>
            </div>

            <div class="offer-card">
                <h3>Happy Hour</h3>
                <p>4:00 PM – 6:00 PM</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= CONTACT SECTION ================= -->
<section id="contact" class="contact container">
    <h2 class="section-title">Contact Us</h2>

    <form>
        <input type="text" placeholder="Your Name" required>
        <input type="email" placeholder="Your Email" required>
        <textarea placeholder="Your Message..." required></textarea>
        <button type="submit" class="btn">Send Message</button>
    </form>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
