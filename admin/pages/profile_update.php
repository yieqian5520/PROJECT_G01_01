<?php
require_once __DIR__ . '/../partials/auth_guard.php';
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user']['user_id'];

$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($fullName === '') {
  header("Location: ./profile_edit.php?err=empty");
  exit;
}

$uid = (int)$userId;
$stmt = $mysqli->prepare("UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?");
if ($stmt) {
  $stmt->bind_param('ssi', $fullName, $phone, $uid);
  $stmt->execute();
  $stmt->close();
}

$_SESSION['user']['full_name'] = $fullName;

header("Location: ./profile_view.php?msg=updated");
exit;
