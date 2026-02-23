<?php
session_start();
$db = require __DIR__ . "/../config/config.php";

if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  $_SESSION['flash_error'] = "Invalid CSRF token.";
  header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
  exit();
}

$id    = (int)($_POST['id'] ?? 0);
$name  = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($id <= 0 || $name === '') {
  $_SESSION['flash_error'] = "Invalid input.";
  header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
  exit();
}

// Optional: confirm staff exists (and is admin/staff)
$chk = $db->prepare("SELECT id FROM user WHERE id=? AND role IN ('admin','staff') LIMIT 1");
$chk->bind_param("i", $id);
$chk->execute();
if ($chk->get_result()->num_rows === 0) {
  $_SESSION['flash_error'] = "Staff not found.";
  header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
  exit();
}

// ✅ Only update name + phone
$stmt = $db->prepare("UPDATE user SET name=?, phone=? WHERE id=?");
$stmt->bind_param("ssi", $name, $phone, $id);

if ($stmt->execute()) {
  $_SESSION['flash_success'] = "Staff updated successfully.";
} else {
  $_SESSION['flash_error'] = "Failed to update staff.";
}

header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
exit();