<?php
session_start();
if (!isset($_SESSION['email'])) {
  header('Location: index1.php');
  exit();
}

$db = require __DIR__ . "/../config/config.php";

$userId = $_SESSION['id'] ?? null;
if (!$userId) {
  // fallback: find by email
  $email = $_SESSION['email'];
  $stmt = $db->prepare("SELECT id FROM user WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  if (!$res) {
    session_destroy();
    header('Location: index1.php');
    exit();
  }
  $userId = (int)$res['id'];
  $_SESSION['id'] = $userId;
}

$name  = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($name === '' || $phone === '') {
  header("Location: dashboard.php?err=missing"); // change to your page name
  exit();
}

// handle photo if provided
$newImagePath = null;

if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
  $tmpName = $_FILES['profile_photo']['tmp_name'];
  $origName = $_FILES['profile_photo']['name'];

  $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
  $allowed = ['jpg', 'jpeg', 'png', 'webp'];

  if (!in_array($ext, $allowed, true)) {
    header("Location: dashboard.php?err=badfile");
    exit();
  }

  // make sure folder exists
  $uploadDir = __DIR__ . "/../uploads/profile/";
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  // unique filename
  $fileName = "u{$userId}_" . time() . "." . $ext;
  $destFsPath = $uploadDir . $fileName;

  if (!move_uploaded_file($tmpName, $destFsPath)) {
    header("Location: dashboard.php?err=uploadfail");
    exit();
  }

  // path saved in DB (relative to dashboard file)
  $newImagePath = "../uploads/profile/" . $fileName;
}

// update DB (with or without photo)
if ($newImagePath !== null) {
  $stmt = $db->prepare("UPDATE user SET name = ?, phone = ?, profile_image = ? WHERE id = ?");
  $stmt->bind_param("sssi", $name, $phone, $newImagePath, $userId);
} else {
  $stmt = $db->prepare("UPDATE user SET name = ?, phone = ? WHERE id = ?");
  $stmt->bind_param("ssi", $name, $phone, $userId);
}

$stmt->execute();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
    header("Location: staff_dashboard.php?tab=profile&saved=1");
} else {
    header("Location: dashboard.php?tab=profile&saved=1");
}
exit();                           

exit();
