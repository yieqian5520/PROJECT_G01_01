<?php
session_start();
$db = require __DIR__ . "/../config/config.php";

if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  $_SESSION['flash_error'] = "Invalid CSRF token.";
  header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
  exit();
}

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role  = trim($_POST['role'] ?? 'staff');
$pass  = $_POST['password'] ?? '';

$allowedRoles = ['admin','staff'];
if (!in_array($role, $allowedRoles, true)) $role = 'staff';

if ($name === '' || $email === '' || $pass === '') {
  $_SESSION['flash_error'] = "Please fill in required fields.";
  header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
  exit();
}

// check email exists
$email = strtolower(trim($_POST['email'] ?? ''));

$chk = $db->prepare("SELECT id FROM user WHERE LOWER(email) = ? LIMIT 1");
$chk->bind_param("s", $email);
$chk->execute();
$chk->store_result();

if ($chk->num_rows > 0) {
  $_SESSION['flash_error'] = "This email is already registered.";
  header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
  exit();
}
$chk->bind_param("s", $email);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
  $_SESSION['flash_error'] = "Email already exists.";
  header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
  exit();
}

$password = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT INTO user (name, phone, profile_image, email, role, password)
                      VALUES (?, ?, NULL, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $phone, $email, $role, $password);

if ($stmt->execute()) {
  $_SESSION['flash_success'] = "Staff added successfully.";
} else {
  $_SESSION['flash_error'] = "Failed to add staff.";
}

header("Location: " . ($_POST['return_to'] ?? "dashboard.php?tab=staff"));
exit();