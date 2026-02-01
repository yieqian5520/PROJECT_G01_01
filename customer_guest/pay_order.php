<?php
session_start();
include_once "dbcon.php";

if (!isset($_SESSION['auth_user']['id'])) {
    header("Location: login.php");
    exit;
}

$order_id = (int)($_POST['order_id'] ?? 0);
$method   = $_POST['payment_method'] ?? '';

$allowed = ['Cash','TNG','Credit Card'];
if ($order_id <= 0 || !in_array($method, $allowed, true)) {
    header("Location: order_status.php?latest=1");
    exit;
}

// Make sure this order belongs to this user
$uid = (int)$_SESSION['auth_user']['id'];

$stmt = $con->prepare("
    UPDATE orders
    SET payment_status='PAID',
        payment_method=?,
        paid_at=NOW()
    WHERE id=? AND user_id=?
");
$stmt->bind_param("sii", $method, $order_id, $uid);
$stmt->execute();

// Redirect back to latest order page with message
header("Location: order_status.php?latest=1&paid=1");
exit;
