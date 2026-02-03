<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

$db = require __DIR__ . "/../config/config.php";

/** Load logged-in user (admin/staff) from your `user` table */
$userId = $_SESSION['id'] ?? null;

if ($userId) {
  $uStmt = $db->prepare("SELECT id, role FROM user WHERE id = ?");
  $uStmt->bind_param("i", $userId);
} else {
  $email = $_SESSION['email'];
  $uStmt = $db->prepare("SELECT id, role FROM user WHERE email = ?");
  $uStmt->bind_param("s", $email);
}
$uStmt->execute();
$me = $uStmt->get_result()->fetch_assoc();

if (!$me) {
  session_destroy();
  header("Location: index1.php");
  exit();
}

$role = strtolower(trim($me['role'] ?? ''));

/** Allow ONLY admin + staff */
if (!in_array($role, ['admin', 'staff'], true)) {
  $_SESSION['flash_error'] = "You are not allowed to delete orders.";
  header("Location: dashboard.php?tab=orders");
  exit();
}

/** CSRF */
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  $_SESSION['flash_error'] = "Invalid CSRF token.";
  $fallback = ($role === 'staff') ? "staff_dashboard.php?tab=orders" : "dashboard.php?tab=orders";
  header("Location: " . $fallback);
  exit();
}

/** Orders selected */
$orderIds = $_POST['order_ids'] ?? [];
if (!is_array($orderIds) || empty($orderIds)) {
  $_SESSION['flash_error'] = "No orders selected.";
  $fallback = ($role === 'staff') ? "staff_dashboard.php?tab=orders" : "dashboard.php?tab=orders";
  header("Location: " . $fallback);
  exit();
}

$orderIds = array_values(array_filter(array_map('intval', $orderIds), fn($v) => $v > 0));
if (empty($orderIds)) {
  $_SESSION['flash_error'] = "No valid orders selected.";
  $fallback = ($role === 'staff') ? "staff_dashboard.php?tab=orders" : "dashboard.php?tab=orders";
  header("Location: " . $fallback);
  exit();
}

/** Safe return_to (avoid open redirect) */
$returnTo = $_POST['return_to'] ?? '';
$allowedReturn = [
  'dashboard.php?tab=orders',
  'staff_dashboard.php?tab=orders',
];
if (!in_array($returnTo, $allowedReturn, true)) {
  $returnTo = ($role === 'staff') ? 'staff_dashboard.php?tab=orders' : 'dashboard.php?tab=orders';
}

$db->begin_transaction();

try {
  $in = implode(',', array_fill(0, count($orderIds), '?'));
  $types = str_repeat('i', count($orderIds));

  // delete items first
  $stmt1 = $db->prepare("DELETE FROM order_items WHERE order_id IN ($in)");
  $stmt1->bind_param($types, ...$orderIds);
  $stmt1->execute();

  // delete orders
  $stmt2 = $db->prepare("DELETE FROM orders WHERE id IN ($in)");
  $stmt2->bind_param($types, ...$orderIds);
  $stmt2->execute();

  $db->commit();

  $_SESSION['flash_success'] = "Deleted " . count($orderIds) . " order(s) successfully.";
} catch (Throwable $e) {
  $db->rollback();
  $_SESSION['flash_error'] = "Delete failed: " . $e->getMessage();
}

header("Location: " . $returnTo);
exit();
