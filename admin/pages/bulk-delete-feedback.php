<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

$db = require __DIR__ . "/../config/config.php";

/* CSRF */
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
  $_SESSION['flash_error'] = "Invalid request (CSRF).";
  header("Location: dashboard.php?tab=feedback");
  exit();
}

/* Role: admin only */
$userId = $_SESSION['id'] ?? null;

if ($userId) {
  $u = $db->prepare("SELECT role FROM user WHERE id=?");
  $u->bind_param("i", $userId);
} else {
  $email = $_SESSION['email'];
  $u = $db->prepare("SELECT role FROM user WHERE email=?");
  $u->bind_param("s", $email);
}

$u->execute();
$me = $u->get_result()->fetch_assoc();
$role = strtolower(trim($me['role'] ?? ''));

if ($role !== 'admin') {
  $_SESSION['flash_error'] = "You are not allowed to delete feedback.";
  header("Location: dashboard.php?tab=feedback");
  exit();
}

/* IDs */
$ids = $_POST['feedback_ids'] ?? [];
if (!is_array($ids) || empty($ids)) {
  $_SESSION['flash_error'] = "No feedback selected.";
  header("Location: dashboard.php?tab=feedback");
  exit();
}

$ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
if (empty($ids)) {
  $_SESSION['flash_error'] = "No valid feedback selected.";
  header("Location: dashboard.php?tab=feedback");
  exit();
}

$in = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$stmt = $db->prepare("DELETE FROM feedback_message WHERE id IN ($in)");
$stmt->bind_param($types, ...$ids);
$stmt->execute();

$_SESSION['flash_success'] = "Deleted {$stmt->affected_rows} feedback item(s).";
header("Location: dashboard.php?tab=feedback");
exit();
