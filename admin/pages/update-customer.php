<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }
$db = require __DIR__ . "/../config/config.php";

if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    die("Invalid CSRF token");
}

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$verify_status = (int)($_POST['verify_status'] ?? 0);

if ($id <= 0 || $name === '' || $phone === '' || $address === '') {
    die("Invalid input");
}

$stmt = $db->prepare("UPDATE users SET name=?, phone=?, address=?, verify_status=? WHERE id=?");
$stmt->bind_param("sssii", $name, $phone, $address, $verify_status, $id);
$stmt->execute();

header("Location: staff_dashboard.php#customers");
exit();
