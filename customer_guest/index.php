<?php
session_start();
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
            <a href="#menu" class="btn">View Menu</a>
        </div>
    </div>
</section>

<!-- ================= MENU SECTION ================= -->
<section id="menu" class="menu container">
    <h2 class="section-title">Our Menu</h2>

    <div class="menu-grid">
        <!-- Menu Item 1 -->
        <div class="menu-item">
            <img src="image/background2.jpg" alt="Espresso">    
            <h3>Espresso</h3>
            <p>Strong and bold coffee shot</p>
            <span>RM 8.00</span>
            <br>
            <a href="#" class="btn">Add to Cart</a>
        </div>

        <!-- Menu Item 2 -->
        <div class="menu-item">
            <img src="image/background2.jpg" alt="Cappuccino">
            <h3>Cappuccino</h3>
            <p>Rich coffee with foamy milk</p>
            <span>RM 10.00</span>
            <br>
            <a href="#" class="btn">Add to Cart</a>
        </div>

        <!-- Menu Item 3 -->
        <div class="menu-item">
            <img src="image/background2.jpg" alt="Latte">
            <h3>Latte</h3>
            <p>Smooth coffee with milk</p>
            <span>RM 12.00</span>
            <br>
            <a href="#" class="btn">Add to Cart</a>
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
        <a href="about.php" class="btn">Visit Us</a>
    </div>

    <div class="about-img">
        <img src="image/About.png" alt="About Us">
    </div>
</section>

<!-- ================= CONTACT SECTION ================= -->
<section id="contact" class="contact container">
    <h2 class="section-title">Contact Us</h2>

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
