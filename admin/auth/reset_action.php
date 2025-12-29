<?php
require_once __DIR__ . '/../partials/session.php';
require_once __DIR__ . '/../config/db.php';

$token    = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($token === '') {
  header("Location: ../pages/login.php?err=bad_token");
  exit;
}

if ($password === '' || $confirm === '') {
  $_SESSION['flash_error'] = 'Please fill in both password fields.';
  header("Location: ../pages/reset_password.php?token=" . urlencode($token));
  exit;
}

if ($password !== $confirm) {
  $_SESSION['flash_error'] = 'Passwords do not match.';
  header("Location: ../pages/reset_password.php?token=" . urlencode($token));
  exit;
}

function valid_password($pw) {
  if (strlen($pw) < 8) return false;
  if (!preg_match('/[a-z]/', $pw)) return false; // at least one lowercase
  if (!preg_match('/[0-9]/', $pw)) return false; // at least one number
  return true;                                      
}

if (!valid_password($password)) {
  $_SESSION['flash_error'] = 'Password too weak. Use at least 8 chars with upper, lower and number.';
  header("Location: ../pages/reset_password.php?token=" . urlencode($token));
  exit;
}

$tokenHash = hash('sha256', $token);

$stmt = $mysqli->prepare(
  "SELECT reset_id, user_id, expires_at, used_at FROM password_resets WHERE token_hash = ? LIMIT 1"
);
if (!$stmt) {
  header("Location: ../pages/login.php?err=bad_token");
  exit;
}
$stmt->bind_param('s', $tokenHash);
$stmt->execute();
$res = $stmt->get_result();
$reset = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$reset) {
  header("Location: ../pages/login.php?err=bad_token");
  exit;
}

if (!empty($reset['used_at'])) {
  header("Location: ../pages/login.php?err=token_used");
  exit;
}

if (strtotime($reset['expires_at']) < time()) {
  header("Location: ../pages/login.php?err=token_expired");
  exit;
}

$newHash = password_hash($password, PASSWORD_DEFAULT);
$userId  = (int)$reset['user_id'];
$resetId = (int)$reset['reset_id'];

// Update user's password
$stmt = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
if ($stmt) {
  $stmt->bind_param('si', $newHash, $userId);
  $stmt->execute();
  $stmt->close();
}

// Mark token as used
$stmt = $mysqli->prepare("UPDATE password_resets SET used_at = NOW() WHERE reset_id = ?");
if ($stmt) {
  $stmt->bind_param('i', $resetId);
  $stmt->execute();
  $stmt->close();
}

header("Location: ../pages/login.php?msg=reset_ok");
exit;
