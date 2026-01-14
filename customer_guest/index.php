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
            <a href="menu.php   " class="btn">View Menu</a>
        </div>
    </div>
</section>

<!-- ================= MENU SECTION ================= -->
<section id="menu" class="menu container">
    <h2 class="section-title">Our Menu</h2>

    <div class="menu-grid">
        <!-- Menu Item 1 -->
        <div class="menu-item">
            <img src="image/Espresso.jpeg" alt="Espresso">    
            <h3>Espresso</h3>
            <p>Strong and bold coffee shot</p>
            <h1><span>RM 8.00</span></h1>
        </div>

        <!-- Menu Item 2 -->
        <div class="menu-item">
            <img src="image/Matcha.jpeg" alt="Matcha">
            <h3>Matcha</h3>
            <p>Smooth Japanese green tea blended with milk</p>
            <h1><span>RM 12.00</span></h1>
        </div>

        <!-- Menu Item 3 -->
        <div class="menu-item">
            <img src="image/Biscoff Cake.jpg" alt="Biscoff Cake">
            <h3>Biscoff Cake</h3>
            <p>Soft cake layered with Biscoff spread</p>
            <h1><span>RM 12.00</span></h1>
        </div>

        <!-- Menu Item 4 -->
        <div class="menu-item">
            <img src="image/Japanese Cream Puff.jpg" alt="Japanese Cream Puff">
            <h3>Japanese Cream Puff</h3>
            <p>Light pastry filled with creamy custard</p>
            <h1><span>RM 6.00</span></h1>
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


<?php
include_once __DIR__ . "/includes/footer.php";
?>
