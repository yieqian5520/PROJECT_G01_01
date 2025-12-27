<?php
require_once __DIR__ . '/../partials/auth_guard.php';
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user']['user_id'];
$old = $_POST['old_password'] ?? '';
$new = $_POST['new_password'] ?? '';

if ($old === '' || $new === '') {
  header("Location: ./profile_edit.php?err=empty");
  exit;
}

$uid = (int)$userId;
$stmt = $mysqli->prepare("SELECT password_hash FROM users WHERE user_id = ? LIMIT 1");
if (!$stmt) {
  header("Location: ./profile_edit.php?err=wrong_old");
  exit;
}
$stmt->bind_param('i', $uid);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$row || !password_verify($old, $row['password_hash'])) {
  header("Location: ./profile_edit.php?err=wrong_old");
  exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
if ($stmt) {
  $stmt->bind_param('si', $newHash, $uid);
  $stmt->execute();
  $stmt->close();
}

header("Location: ./profile_view.php?msg=pass_changed");
exit;
