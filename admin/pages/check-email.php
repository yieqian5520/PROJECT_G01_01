<?php
session_start();
$db = require __DIR__ . "/../config/config.php";

// Optional: require login
if (!isset($_SESSION['email'])) {
  http_response_code(403);
  echo "forbidden";
  exit();
}

$email = trim($_POST['email'] ?? '');
if ($email === '') {
  echo "ok";
  exit();
}

$stmt = $db->prepare("SELECT id FROM user WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

echo ($stmt->get_result()->num_rows > 0) ? "exists" : "ok";