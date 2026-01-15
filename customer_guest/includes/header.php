<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login status
$isLoggedIn = isset($_SESSION['authenticated']);
$user = $_SESSION['auth_user'] ?? [];

// Profile image fallback
$profileImg = !empty($user['profile_image'])
    ? htmlspecialchars($user['profile_image'])
    : 'https://via.placeholder.com/60';

// Default cart count
$cart_count = 0;

// Include database connection only if needed
if ($isLoggedIn) {
    require_once __DIR__ . '/../dbcon.php';

    $session_id = session_id();

    // Prepare statement safely
    $stmt = mysqli_prepare(
        $con,
        "SELECT COALESCE(SUM(quantity),0) AS total FROM cart_items WHERE session_id = ?"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $session_id);
        mysqli_stmt_execute($stmt);

        // Get result safely
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $cart_count = (int)($row['total'] ?? 0);
        }

        mysqli_stmt_close($stmt);
    } else {
        // Optional: Log or handle prepare error
        // $cart_count stays 0 if query fails
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pucks Coffee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<nav class="navbar container">

    <!-- LOGO -->
    <div class="logo d-flex align-items-center">
        <img src="image/logo.jpg" alt="Pucks Coffee Logo" width="40" class="me-2">
        <a href="index.php" class="text-white fw-bold text-decoration-none">
            Pucks Coffee
        </a>
    </div>

    <!-- NAV LINKS -->
    <ul class="nav-links mb-0 d-flex align-items-center">

        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>

        <!-- CART ICON -->
        <?php if ($isLoggedIn): ?>
        <li class="position-relative ms-3">
            <a href="cart.php" class="nav-icon position-relative">
                <i class="bi bi-cart fs-4"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $cart_count ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>
        <?php endif; ?>

        <!-- USER DROPDOWN -->
        <li class="nav-item dropdown ms-3">
            <a class="nav-icon dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <?php if ($isLoggedIn): ?>
                    <img src="<?= $profileImg ?>" class="profile-icon rounded-circle" width="35" height="35">
                <?php else: ?>
                    <i class="bi bi-person-circle fs-3"></i>
                <?php endif; ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end p-3 shadow" style="min-width:260px">

                <div class="text-center mb-3">
                    <img src="<?= $profileImg ?>" class="rounded-circle mb-2" width="70" height="70">
                    <div class="fw-bold"><?= $isLoggedIn ? htmlspecialchars($user['username']) : 'Guest User' ?></div>
                    <small class="text-muted">
                        <?= $isLoggedIn ? htmlspecialchars($user['email']) : 'Please login or register' ?>
                    </small>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <?php if ($isLoggedIn): ?>
                        <a href="dashboard.php" class="btn btn-outline-dark uniform-btn">
                            <i class="bi bi-speedometer2 me-2"></i> Profile
                        </a>

                        <form action="logout.php" method="POST">
                            <button type="submit" class="btn btn-outline-dark uniform-btn w-100">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-dark uniform-btn">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login
                        </a>
                        <a href="register.php" class="btn btn-outline-dark uniform-btn">
                            <i class="bi bi-person-plus me-2"></i> Register
                        </a>
                    <?php endif; ?>
                </div>

            </ul>
        </li>

    </ul>
</nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
