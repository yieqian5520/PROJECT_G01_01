<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['authenticated']);
$user = $_SESSION['auth_user'] ?? [];

$profileImg = !empty($user['profile_image'])
    ? $user['profile_image']
    : 'https://via.placeholder.com/60';

$cart_count = 0;

if ($isLoggedIn) {
    include __DIR__ . "/../dbcon.php"; // header.php in /includes, go up 1 level
    $session_id = session_id();

    $stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(quantity),0) AS total FROM cart_items WHERE session_id=?");
    mysqli_stmt_bind_param($stmt, "s", $session_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($res);

    $cart_count = (int)($data['total'] ?? 0);
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
    <ul class="nav-links d-flex align-items-center gap-4 mb-0">

        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>

        <!-- CART ICON -->
       <?php if ($isLoggedIn): ?>
  <li class="position-relative">
      <a href="cart.php" class="nav-icon">
          <i class="bi bi-cart"></i>

          <?php if ($cart_count > 0): ?>
              <span class="cart-badge"><?= $cart_count ?></span>
          <?php endif; ?>
      </a>
  </li>
<?php endif; ?>

        <!-- USER DROPDOWN -->
        <li class="nav-item dropdown">
            <a class="nav-icon dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <?php if ($isLoggedIn): ?>
                    <img src="<?= $profileImg ?>" class="rounded-circle profile-icon">
                <?php else: ?>
                    <i class="bi bi-person-circle"></i>
                <?php endif; ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end p-3 shadow" style="min-width:260px">

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