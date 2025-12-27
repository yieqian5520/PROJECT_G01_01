<?php
require_once __DIR__ . '/../partials/session.php';
require_once __DIR__ . '/../config/db.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
  header("Location: ../pages/login.php?err=empty");
  exit;
}

$stmt = $mysqli->prepare("SELECT user_id, full_name, email, role, status, password_hash FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
  // Fail closed (avoid leaking SQL details)
  header("Location: ../pages/login.php?err=invalid");
  exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

if ($user && ($user['status'] ?? '') === 'active' && password_verify($password, $user['password_hash'] ?? '')) {
  $_SESSION['user'] = [
    'user_id' => $user['user_id'],
    'full_name' => $user['full_name'],
    'email' => $user['email'],
    'role' => $user['role'],
  ];

  header("Location: ../pages/dashboard.php");
  exit;
}

header("Location: ../pages/login.php?err=invalid");
exit;
