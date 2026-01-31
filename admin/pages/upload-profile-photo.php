<?php
session_start();

if (!isset($_SESSION['email'])) {
  header("Location: index1.php");
  exit();
}

$db = require __DIR__ . "/../config/config.php";

$userId = $_SESSION['id'] ?? null;
if (!$userId) {
  header("Location: index1.php");
  exit();
}

if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
  header("Location: dashboard.php?saved=1#profile");
  exit();
}

$file = $_FILES['profile_photo'];

// Basic validation
$maxSize = 2 * 1024 * 1024; // 2MB
if ($file['size'] > $maxSize) {
  die("File too large (max 2MB).");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$allowed = [
  'image/jpeg' => 'jpg',
  'image/png'  => 'png',
  'image/webp' => 'webp'
];

if (!isset($allowed[$mime])) {
  die("Only JPG, PNG, WEBP allowed.");
}

$ext = $allowed[$mime];

// Ensure upload folder exists
$uploadDir = __DIR__ . "/../../uploads/profile/";
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

// Generate unique name
$filename = $userId . "_" . time() . "." . $ext;
$targetPath = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
  die("Upload failed.");
}

// Save relative path to DB (relative to your dashboard page)
$dbPath = "../../uploads/profile/" . $filename;

// (Optional) delete old file (avoid deleting default image)
$stmtOld = $db->prepare("SELECT profile_image FROM user WHERE id = ?");
$stmtOld->bind_param("i", $userId);
$stmtOld->execute();
$old = $stmtOld->get_result()->fetch_assoc();
$stmtOld->close();

if (!empty($old['profile_image']) && str_starts_with($old['profile_image'], "../uploads/profile/")) {
  $oldAbs = __DIR__ . "/../" . ltrim(str_replace("../", "", $old['profile_image']), "/");
  if (is_file($oldAbs)) @unlink($oldAbs);
}

// Update DB
$stmt = $db->prepare("UPDATE user SET profile_image = ? WHERE id = ?");
$stmt->bind_param("si", $dbPath, $userId);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php#profile");
exit();
