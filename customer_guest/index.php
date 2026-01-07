<?php
session_start();
// Home Page
include_once __DIR__ . "/includes/header.php";
?>

<!-- ================= HERO SECTION ================= -->
<section id="home" class="hero">
    <div class="hero-overlay">
        <div class="hero-text">
            <br>
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
        <div class="menu-item">
            <img src="image/background2.jpg" alt="">
            <div class="content">
        <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-half"></i>
        </div>
        </div>
            <h3>Espresso</h3>
            <p>Strong and bold coffee shot</p>
            <span>RM 8.00</span>
            <br>
            <a href="#" class="btn">add to cart</a>
        </div>

        <div class="menu-item">
            <img src="image/background2.jpg" alt="">
            <div class="content">
        <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-half"></i>
        </div>
        </div>
            <h3>Espresso</h3>
            <p>Strong and bold coffee shot</p>
            <span>RM 8.00</span>
            <br>
            <a href="#" class="btn">add to cart</a>
        </div>

        <div class="menu-item">
            <img src="image/background2.jpg" alt="">
            <div class="content">
        <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-half"></i>
        </div>
        </div>
            <h3>Espresso</h3>
            <p>Strong and bold coffee shot</p>
            <span>RM 8.00</span>
            <br>
            <a href="#" class="btn">add to cart</a>
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
        <img src="image/About.png" alt="">
    </div>
</section>

<!-- ================= CONTACT SECTION ================= -->
<section id="contact" class="contact container">
    <h2 class="section-title">Contact Us</h2>

    <form>
        <input type="text" placeholder="Your Name" required>
        <input type="phone" placeholder="Your Phone" required>
        <input type="email" placeholder="Your Email" required>
        <textarea placeholder="Your Message..." required></textarea>
        <button type="submit" class="btn">Send Message</button>
    </form>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
