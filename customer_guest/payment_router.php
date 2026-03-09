<?php
session_start();
include_once "dbcon.php";

if (!isset($_SESSION['auth_user']['id'])) {
    echo "<h3 style='padding:40px;text-align:center;'>Please login first</h3>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<h3 style='padding:40px;text-align:center;'>Invalid request</h3>";
    exit;
}

$order_id = (int)($_POST['order_id'] ?? 0);
$payment_method = trim($_POST['payment_method'] ?? '');

if ($order_id <= 0 || $payment_method === '') {
    echo "<h3 style='padding:40px;text-align:center;'>Missing payment information</h3>";
    exit;
}

$order_q = mysqli_query($con, "
    SELECT id, order_code, user_id, payment_status
    FROM orders
    WHERE id = $order_id
    LIMIT 1
");

if (!$order_q || mysqli_num_rows($order_q) === 0) {
    echo "<h3 style='padding:40px;text-align:center;'>Order not found</h3>";
    exit;
}

$order = mysqli_fetch_assoc($order_q);

if ((int)$order['user_id'] !== (int)$_SESSION['auth_user']['id']) {
    echo "<h3 style='padding:40px;text-align:center;'>Unauthorized access</h3>";
    exit;
}

if (($order['payment_status'] ?? '') === 'PAID') {
    header("Location: order_status.php?order=" . urlencode($order['order_code']));
    exit;
}

if ($payment_method === 'Cash') {
    $update = mysqli_query($con, "
        UPDATE orders
        SET payment_method = 'Cash',
            payment_status = 'PAID'
        WHERE id = $order_id
    ");

    if ($update) {
        $_SESSION['payment_success'] = 'Payment successful by Cash.';
        header("Location: order_status.php?order=" . urlencode($order['order_code']));
        exit;
    } else {
        echo "<h3 style='padding:40px;text-align:center;'>Failed to update cash payment</h3>";
        exit;
    }
}

if ($payment_method === 'TNG') {
    header("Location: checkout_tng.php?order_id=" . $order_id);
    exit;
}

if ($payment_method === 'Card') {
    header("Location: checkout_sandbox.php?order_id=" . $order_id);
    exit;
}

echo "<h3 style='padding:40px;text-align:center;'>Invalid payment method</h3>";