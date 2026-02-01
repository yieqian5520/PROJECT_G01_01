<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index1.php");
    exit();
}

$db = require __DIR__ . "/../config/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php?tab=orders");
    exit();
}

if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die("Invalid CSRF token.");
}

$orderId = (int)($_POST['order_id'] ?? 0);
$newStatus = trim($_POST['status'] ?? '');

$statusSteps = ['Confirmed'=>1,'Preparing'=>2,'Ready'=>3];

$curStmt = $db->prepare("SELECT status FROM orders WHERE id=?");
$curStmt->bind_param("i",$orderId);
$curStmt->execute();
$current = $curStmt->get_result()->fetch_assoc();

if (!$current) exit();

$currentStatus = $current['status'] ?? 'Confirmed';

if ($statusSteps[$newStatus] < $statusSteps[$currentStatus]) {
    die("Cannot move status backwards.");
}

if ($orderId <= 0 || !isset($statusSteps[$newStatus])) {
    header("Location: dashboard.php?tab=orders");
    exit();
}

$upd = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
$upd->bind_param("si", $newStatus, $orderId);
$upd->execute();

header("Location: dashboard.php?tab=orders");
exit();
