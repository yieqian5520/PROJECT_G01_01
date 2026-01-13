<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['authenticated']);
$user = $_SESSION['auth_user'] ?? [];

$profileImg = !empty($user['profile_image'])
    ? $user['profile_image']
    : 'https://via.placeholder.com/60';
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

    <!-- Custom -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<nav class="navbar container">

    <!-- LOGO -->
    <div class="logo d-flex align-items-center gap-2">
        <img src="image/logo.jpg" width="40">
        <a href="index.php" class="text-white fw-bold text-decoration-none">
            Pucks Coffee
        </a>
    </div>

    <!-- NAV -->
    <ul class="nav-links d-flex align-items-center gap-3 mb-0">

        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="menu.php" class="active">Menu</a></li>
        <li><a href="about.php" class="active">About</a></li>
        <li><a href="contact.php" class="active">Contact</a></li>

        <!-- CART ICON -->
        <li>
            <a href="cart.php" class="text-white d-flex align-items-center gap-1 position-relative">
                <i class="bi bi-cart fs-4"></i>
                <?php
                // Optional: Show cart count if you want
                $cart_count = 0;
                if(session_id() && isset($_SESSION['authenticated'])){
                    include __DIR__ . "/dbcon.php";
                    $session_id = session_id();
                    $res = mysqli_query($con, "SELECT SUM(quantity) as total FROM cart_items WHERE session_id='$session_id'");
                    $data = mysqli_fetch_assoc($res);
                    $cart_count = $data['total'] ?? 0;
                }
                ?>
                <?php if($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                        <?= $cart_count ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>

        <!-- USER DROPDOWN -->
        <li class="nav-item dropdown">

            <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2"
               href="#"
               data-bs-toggle="dropdown">

                <?php if ($isLoggedIn): ?>
                    <img src="<?= $profileImg ?>" class="rounded-circle" width="34" height="34" style="object-fit:cover;">
                <?php else: ?>
                    <i class="bi bi-person-circle fs-4"></i>
                <?php endif; ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end p-3 shadow" style="min-width:260px">

                <!-- USER INFO -->
                <div class="text-center mb-3">
                    <img src="<?= $profileImg ?>" class="rounded-circle mb-2" width="70" height="70">
                    <div class="fw-bold">
                        <?= $isLoggedIn ? htmlspecialchars($user['username']) : 'Guest User' ?>
                    </div>
                    <small class="text-muted">
                        <?= $isLoggedIn ? htmlspecialchars($user['email']) : 'Please login or register' ?>
                    </small>
                </div>

                <hr>

                <!-- UNIFORM BUTTONS -->
                <div class="d-grid gap-2">

                    <a href="dashboard.php" class="btn btn-outline-dark uniform-btn">
                        <i class="bi bi-speedometer2 me-2"></i> Profile
                    </a>

                    <a href="login.php" class="btn btn-outline-dark uniform-btn">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                    </a>

                    <a href="register.php" class="btn btn-outline-dark uniform-btn">
                        <i class="bi bi-person-plus me-2"></i> Register
                    </a>

                    <?php if ($isLoggedIn): ?>
                        <form action="logout.php" method="POST">
                            <button type="submit" class="btn btn-outline-dark uniform-btn w-100">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    <?php endif; ?>

                </div>

            </ul>
        </li>

    </ul>
</nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
