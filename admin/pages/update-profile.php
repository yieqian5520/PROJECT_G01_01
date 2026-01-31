<?php
session_start();

if (!isset($_SESSION['email'])) {
  header('Location: index1.php');
  exit();
}

// FIX #2: correct path because this file is in admin/pages/
$db = require __DIR__ . "/../config/config.php";

$userId = $_SESSION['id'] ?? null;

if (!$userId) {
  $email = $_SESSION['email'];
  $stmt = $db->prepare("SELECT id FROM user WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (!$res) {
    session_destroy();
    header('Location: index1.php');
    exit();
  }

  $userId = (int)$res['id'];
  $_SESSION['id'] = $userId;
}

$returnTo = $_POST['return_to'] ?? 'staff_dashboard.php?tab=profile';

// basic safety: only allow local redirects
if (str_contains($returnTo, '://') || str_starts_with($returnTo, '//')) {
  $returnTo = 'staff_dashboard.php?tab=profile';
}

$name  = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($name === '' || $phone === '') {
  header("Location: {$returnTo}&err=missing");
  exit();
}

$newImagePath = null;

// handle photo if provided
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
  $tmpName  = $_FILES['profile_photo']['tmp_name'];

  // (recommended) validate by MIME instead of extension
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime  = $finfo->file($tmpName);

  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
  ];

  if (!isset($allowed[$mime])) {
    header("Location: {$returnTo}&err=badfile");
    exit();
  }

  $ext = $allowed[$mime];

  // Correct folder: PROJECT_G01_01/uploads/profile/
  $uploadDir = __DIR__ . "/../../uploads/profile/";
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $fileName   = "u{$userId}_" . time() . "." . $ext;
  $destFsPath = $uploadDir . $fileName;

  if (!move_uploaded_file($tmpName, $destFsPath)) {
    header("Location: {$returnTo}&err=uploadfail");
    exit();
  }

  // FIX #1: must use $fileName (not $filename)
  $newImagePath = "../../uploads/profile/" . $fileName;
}

// update DB
if ($newImagePath !== null) {
  $stmt = $db->prepare("UPDATE user SET name = ?, phone = ?, profile_image = ? WHERE id = ?");
  $stmt->bind_param("sssi", $name, $phone, $newImagePath, $userId);
} else {
  $stmt = $db->prepare("UPDATE user SET name = ?, phone = ? WHERE id = ?");
  $stmt->bind_param("ssi", $name, $phone, $userId);
}

$stmt->execute();
$stmt->close();

$join = str_contains($returnTo, '?') ? '&' : '?';
header("Location: {$returnTo}{$join}saved=1");
exit();
exit();
