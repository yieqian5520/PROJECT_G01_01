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

$statuses = $_POST['statuses'] ?? [];
if (!is_array($statuses) || empty($statuses)) {
    header("Location: " . $redirect);
    exit();
}

$statusSteps = [
    'Confirmed' => 1,
    'Preparing' => 2,
    'Ready'     => 3,
];
$allowed = array_keys($statusSteps);

$upd = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
$upd->bind_param("si", $status, $id);

foreach ($statuses as $orderId => $newStatus) {
    $id = (int)$orderId;
    $status = trim((string)$newStatus);

    if ($id <= 0) continue;
    if (!in_array($status, $allowed, true)) continue;

    $upd->execute();
}

header("Location: " . $redirect);
exit();
