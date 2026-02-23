<?php
session_start();
$db = require __DIR__ . "/../config/config.php";

if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  $_SESSION['flash_error'] = "Invalid CSRF token.";
  header("Location: dashboard.php?tab=staff");
  exit();
}

$ids = $_POST['staff_ids'] ?? [];
$ids = array_map('intval', (array)$ids);
$ids = array_values(array_filter($ids, fn($x) => $x > 0));

if (empty($ids)) {
  $_SESSION['flash_error'] = "No staff selected.";
  header("Location: dashboard.php?tab=staff");
  exit();
}

// prevent deleting own account (if you store id in session)
$myId = (int)($_SESSION['id'] ?? 0);
$ids = array_values(array_filter($ids, fn($x) => $x !== $myId));

if (empty($ids)) {
  $_SESSION['flash_error'] = "You cannot delete your own account.";
  header("Location: dashboard.php?tab=staff");
  exit();
}

$in = implode(',', array_fill(0, count($ids), '?'));
$sql = "DELETE FROM user WHERE id IN ($in) AND role IN ('admin','staff')";

$stmt = $db->prepare($sql);
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);

if ($stmt->execute()) {
  $_SESSION['flash_success'] = "Deleted staff successfully.";
} else {
  $_SESSION['flash_error'] = "Failed to delete staff.";
}

header("Location: dashboard.php?tab=staff");
exit();