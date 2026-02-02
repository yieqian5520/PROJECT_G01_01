<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

$db = require __DIR__ . "/../config/config.php";

if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $_SESSION['flash_error'] = "Invalid request (CSRF).";
    header("Location: dashboard.php?tab=customers");
    exit();
}

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$verify_status = (int)($_POST['verify_status'] ?? 0);

if ($id <= 0 || $name === '' || $phone === '' || $address === '') {
    $_SESSION['flash_error'] = "Please fill in all required fields.";
    header("Location: edit-customer-admin.php?id=".$id);
    exit();
}

$verify_status = ($verify_status === 1) ? 1 : 0;

$stmt = $db->prepare("UPDATE users SET name=?, phone=?, address=?, verify_status=? WHERE id=?");
$stmt->bind_param("sssii", $name, $phone, $address, $verify_status, $id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Customer updated successfully.";
} else {
    $_SESSION['flash_error'] = "Failed to update customer.";
}

header("Location: dashboard.php?tab=customers");
exit();
