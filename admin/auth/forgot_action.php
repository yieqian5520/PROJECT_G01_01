<?php
require_once __DIR__ . '/../partials/session.php';
require_once __DIR__ . '/../config/db.php';

$email = trim($_POST['email'] ?? '');

if ($email === '') {
  header("Location: ../pages/forgot_password.php?err=empty");
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header("Location: ../pages/forgot_password.php?err=invalid_email");
  exit;
}

// Find user (do not reveal if email exists)
$stmt = $mysqli->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
  $_SESSION['flash'] = "If that email exists, a reset link has been sent.";
  header("Location: ../pages/forgot_password.php");
  exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

// Always show generic message
$_SESSION['flash'] = "If that email exists, a reset link has been sent.";

if (!$user) {
  header("Location: ../pages/forgot_password.php?msg=sent");
  exit;
}

// Create token
$rawToken  = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $rawToken);
$expires   = date('Y-m-d H:i:s', time() + 900); // 15 minutes
$userId    = (int)$user['user_id'];

// Invalidate old tokens
$stmt = $mysqli->prepare("UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL");
if ($stmt) {
  $stmt->bind_param('i', $userId);
  $stmt->execute();
  $stmt->close();
}

// Store new token (expects table columns: user_id, token_hash, expires_at)
$stmt = $mysqli->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
if ($stmt) {
  $stmt->bind_param('iss', $userId, $tokenHash, $expires);
  $stmt->execute();
  $stmt->close();
}

// DEMO MODE: show link on screen
$_SESSION['demo_reset_link'] = "reset_password.php?token=" . urlencode($rawToken);

header("Location: ../pages/forgot_password.php?msg=sent");
exit;
