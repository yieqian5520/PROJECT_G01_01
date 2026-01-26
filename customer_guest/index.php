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
            <a href="menu.php" class="btn">View Menu</a>
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

<!-- ================= FEEDBACK SECTION ================= -->
<section class="container py-5">
    <h2 class="section-title">What Our Customers Say</h2>

    <div class="row justify-content-center">

        <?php
        include_once __DIR__ . "/dbcon.php";

        $query = "
            SELECT f.comment, f.rating, f.created_at,
                   u.name, u.profile_image
            FROM feedback_message f
            JOIN users u ON f.user_id = u.id
            ORDER BY f.created_at DESC
            LIMIT 3
        ";

        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
        ?>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">

                    <img
                      src="<?= !empty($row['profile_image']) ? $row['profile_image'] : 'https://via.placeholder.com/80' ?>"
                      class="rounded-circle mb-3"
                      width="80"
                      height="80"
                      style="object-fit:cover;"
                    >

                    <h6><?= htmlspecialchars($row['name']) ?></h6>

                    <!-- Stars -->
                    <div class="mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $row['rating'] ? '⭐' : '☆' ?>
                        <?php endfor; ?>
                    </div>

                    <p class="small">
                        “<?= htmlspecialchars($row['comment']) ?>”
                    </p>

                    <small class="text-muted">
                        <?= date("d M Y", strtotime($row['created_at'])) ?>
                    </small>

                </div>
            </div>
        </div>

        <?php
            endwhile;
        else:
        ?>
            <p class="text-center">No feedback yet. Be the first to review us!</p>
        <?php endif; ?>

    </div>

    <!-- View All Feedback -->
    <div class="text-center mt-3">
        <a href="feedback.php" class="btn">View All Feedback</a>
    </div>
</section>


<?php
include_once __DIR__ . "/includes/footer.php";
?>
