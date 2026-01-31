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
        WHERE order_code='" . mysqli_real_escape_string($con, $order) . "'
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

// Fetch order items safely
$items_res = mysqli_query($con, "SELECT * FROM order_items WHERE order_id=" . ($o['id'] ?? 0));
$items = [];
if ($items_res && mysqli_num_rows($items_res) > 0) {
    while ($row = mysqli_fetch_assoc($items_res)) {
        $items[] = [
    'item_name' => $row['menu_name'],   // ✅ correct column
    'item_image' => 'image/default.jpg', // optional placeholder
    'quantity' => $row['quantity'],
    'price' => $row['price'],
];

    }
}

// Map order status to step
$statusSteps = [
    'Confirmed' => 1,
    'Preparing' => 2,
    'Ready' => 3,
];

$currentStep = $statusSteps[$o['status']] ?? 0;

?>

<link rel="stylesheet" href="assets/styleorder.css">


<section class="order-wrapper">
    <!-- Header -->
    <div class="order-header">
        <h2>Order Details #<?= htmlspecialchars($o['order_code'] ?? 'N/A') ?></h2>
        <p>Order Date: <?= !empty($o['created_at']) ? date("M d, Y H:i", strtotime($o['created_at'])) : 'N/A' ?></p>
    </div>

    <!-- Order Status Steps -->
    <div class="order-steps">
        <div class="step <?= $currentStep >= 1 ? 'active' : '' ?>">
            <div class="step-icon">✓</div>
            <p>Order Confirmed</p>
        </div>
        <div class="step <?= $currentStep >= 2 ? 'active' : '' ?>">
            <div class="step-icon">⌛</div>
            <p>Preparing</p>
        </div>
        <div class="step <?= $currentStep >= 3 ? 'active' : '' ?>">
            <div class="step-icon">📦</div>
            <p>Ready for Pickup</p>
        </div>
    </div>

    <!-- Pickup Code -->
    <?php if (!empty($o['pickup_code'])): ?>
        <div class="pickup-code">
            <h4>Pickup Code</h4>
            <p><?= htmlspecialchars($o['pickup_code']) ?></p>
        </div>
    <?php endif; ?>

    <!-- Items -->
    <?php if (!empty($items)): ?>
    <?php foreach ($items as $item): ?>
        <div class="order-item">
            <img src="<?= htmlspecialchars($item['item_image']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
            <div class="item-info">
                <p class="item-name"><?= htmlspecialchars($item['item_name']) ?></p>
                <p class="item-qty">Qty: <?= $item['quantity'] ?></p>
                <p class="item-price">RM<?= number_format($item['price'], 2) ?> </p>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No items found for this order.</p>
<?php endif; ?>


<!-- Payment Methods -->
<form action="order_history.php" method="GET" class="payment-methods">
    <h4>Payment Method</h4>

    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">

    <?php foreach (['Cash','TNG','Credit Card'] as $method): ?>
        <label>
            <input type="radio" name="payment_method" value="<?= $method ?>" required>
            <?= $method ?>
        </label>
    <?php endforeach; ?>

    <div class="order-summary">
        <p><strong>Total Amount:</strong> RM <?= number_format($o['total'], 2) ?></p>
        <button type="submit" class="btn-pay">Pay Now</button>
    </div>
</form>

</section>

<?php include_once "includes/footer.php"; ?>
