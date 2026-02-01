<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index1.php");
    exit();
}

$db = require __DIR__ . "/../config/config.php";

$redirect = $_POST['return_to'] ?? 'dashboard.php?tab=orders';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $redirect);
    exit();
}

if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die("Invalid CSRF token.");
}

$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0) {
    header("Location: " . $redirect);
    exit();
}

$db->begin_transaction();

try {
    // 1) delete all items of that order
    $delItems = $db->prepare("DELETE FROM order_items WHERE order_id = ?");
    $delItems->bind_param("i", $orderId);
    $delItems->execute();

    // 2) delete the order
    $delOrder = $db->prepare("DELETE FROM orders WHERE id = ?");
    $delOrder->bind_param("i", $orderId);
    $delOrder->execute();

    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    // Optional: log error
}

header("Location: " . $redirect);
exit();
