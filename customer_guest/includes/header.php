<?php
include_once "dbcon.php";

// Check login status
$isLoggedIn = isset($_SESSION['authenticated']);
$user = $_SESSION['auth_user'] ?? [];

// Profile image fallback
$profileImg = !empty($user['profile_image'])
    ? htmlspecialchars($user['profile_image'])
    : 'https://via.placeholder.com/60';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pucks Coffee</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="style.css">

<style>
/* Cart badge centered */
.cart-badge {
    background-color: #ffca28;
    color: #000;
    font-size: 0.75rem;
    font-weight: 700;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    position: absolute;
    top: 0;
    right: 0;
    transform: translate(50%, -50%);
    z-index: 10;
}
</style>

</head>
<body>
    <!-- CART DRAWER (ADD ONLY) -->
<div id="cartDrawer" style="
position:fixed;
top:0;
right:-420px;
width:420px;
height:100%;
background:#fff;
z-index:9999;
transition:.3s;
box-shadow:-4px 0 20px rgba(0,0,0,.15);
">
    <iframe src="cart.php" style="width:100%;height:100%;border:none;"></iframe>
</div>

<div id="cartOverlay" style="
position:fixed;
inset:0;
background:rgba(0,0,0,.4);
z-index:9998;
display:none;
" onclick="closeCart()"></div>

<script>
function openCart(e){
    e.preventDefault();
    document.getElementById('cartDrawer').style.right='0';
    document.getElementById('cartOverlay').style.display='block';
}

function closeCart(){
    document.getElementById('cartDrawer').style.right='-420px';
    document.getElementById('cartOverlay').style.display='none';
}
</script>


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
        <li><a href="feedback.php">Feedback</a></li>

        <!-- CART ICON -->
        <li class="position-relative ms-3">
            <a href="cart.php" class="nav-icon position-relative" onclick="openCart(event)">

                <i class="bi bi-cart fs-4"></i>
                <span class="cart-badge"></span>
            </a>
        </li>
    
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
        <!-- Profile button -->
        <a href="dashboard.php" class="btn btn-outline-dark uniform-btn">
            <i class="bi bi-speedometer2 me-2"></i> Profile
        </a>

        <!-- New: My Orders button -->
        <a href="order_history.php" class="btn btn-outline-dark uniform-btn">
            <i class="bi bi-bag-check me-2"></i> Orders History
        </a>

        <!-- Logout button -->
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
<a href="order_status.php?latest=1" class="btn btn-outline-dark uniform-btn">
    <i class="bi bi-receipt me-2"></i> Latest Order
</a>

    </ul>
</nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Cart badge JS -->
<script>
function updateCartBadge() {
    fetch('cart_count.php')
        .then(res => res.json())
        .then(data => {
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                const qty = data.total_qty || 0;
                badge.textContent = qty;
                badge.style.display = qty > 0 ? 'flex' : 'none';
            }
        });
}

// Initial load
updateCartBadge();

// Optional: auto-refresh every 5s
setInterval(updateCartBadge, 5000);
</script>
