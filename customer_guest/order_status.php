<?php
session_start();
include_once "dbcon.php";
include_once "includes/header.php";

// Ensure user is logged in if checking latest order
if (isset($_GET['latest'])) {
    if (!isset($_SESSION['authenticated'])) {
        echo "<h3 style='padding:40px;text-align:center;'>Please login to view your latest order</h3>";
        include_once "includes/footer.php";
        exit;
    }
    $uid = $_SESSION['auth_user']['id'];
    $res = mysqli_query($con, "
        SELECT * FROM orders
        WHERE user_id=$uid
        ORDER BY id DESC
        LIMIT 1
    ");
} else {
    $order = $_GET['order'] ?? '';
    $res = mysqli_query($con, "
        SELECT * FROM orders
        WHERE order_code='$order'
        LIMIT 1
    ");
}

// Handle no orders found
if (!$res || mysqli_num_rows($res) == 0) {
    echo "<h3 style='padding:40px;text-align:center;'>Order Not Found</h3>";
    include_once "includes/footer.php";
    exit;
}

$o = mysqli_fetch_assoc($res);
?>

<link rel="stylesheet" href="assets/order.css">

<section class="order-wrapper">
    <div class="order-card status">
        <h4>Order Status</h4>

        <p>Order Code</p>
        <h3><?= htmlspecialchars($o['order_code']) ?></h3>

        <span class="status-badge">
            <?= htmlspecialchars($o['status']) ?>
        </span>

        <p class="type">
            <?= htmlspecialchars($o['order_type']) ?>
        </p>

        <a href="order_history.php" class="btn-order">
            View My Orders
        </a>
    </div>
</section>

<?php include_once "includes/footer.php"; ?>
