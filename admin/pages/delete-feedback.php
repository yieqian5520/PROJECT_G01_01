<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index1.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php?tab=feedback");
    exit();
}

if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die("Invalid CSRF token.");
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: dashboard.php?tab=feedback");
    exit();
}

$db = require __DIR__ . "/../config/config.php";

// Optional: allow only admin to delete
$role = $_SESSION['role'] ?? null;
// If you store role in session, uncomment this:
// if ($role !== 'admin') { die("Unauthorized."); }

$stmt = $db->prepare("DELETE FROM feedback_message WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: dashboard.php?tab=feedback");
exit();
