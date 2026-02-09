<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index1.php");
    exit();
}

$db = require __DIR__ . "/../config/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php?tab=orders");
    exit();
}

/** Redirect back to sender (dashboard or staff_dashboard) */
$returnTo = trim($_POST['return_to'] ?? '');
if ($returnTo === '') {
    $returnTo = "dashboard.php?tab=orders"; // fallback
}

/** CSRF */
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die("Invalid CSRF token.");
}

/** Status steps */
$statusSteps = [
    'Confirmed' => 1,
    'Preparing' => 2,
    'Ready'     => 3,
];

/** Helper to validate status */
$validStatus = function($s) use ($statusSteps) {
    $s = trim((string)$s);
    return isset($statusSteps[$s]) ? $s : null;
};

/**
 * MODE A: BULK update (from your "Update All" button)
 * expects: statuses[orderId] => newStatus
 */
if (!empty($_POST['statuses']) && is_array($_POST['statuses'])) {

    // Prepare statements once (faster + cleaner)
    $curStmt = $db->prepare("SELECT status FROM orders WHERE id = ?");
    $updStmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");

    foreach ($_POST['statuses'] as $orderId => $newStatusRaw) {
        $orderId = (int)$orderId;
        if ($orderId <= 0) continue;

        $newStatus = $validStatus($newStatusRaw);
        if ($newStatus === null) continue;

        // Get current status
        $curStmt->bind_param("i", $orderId);
        $curStmt->execute();
        $current = $curStmt->get_result()->fetch_assoc();
        if (!$current) continue;

        $currentStatus = trim($current['status'] ?? 'Confirmed');
        if (!isset($statusSteps[$currentStatus])) {
            $currentStatus = 'Confirmed';
        }

        // Prevent backwards
        if ($statusSteps[$newStatus] < $statusSteps[$currentStatus]) {
            continue; // skip instead of die (bulk friendly)
        }

        // Only update if changed
        if ($newStatus !== $currentStatus) {
            $updStmt->bind_param("si", $newStatus, $orderId);
            $updStmt->execute();
        }
    }

    header("Location: " . $returnTo);
    exit();
}

/**
 * MODE B: SINGLE update (if you still use it elsewhere)
 * expects: order_id + status
 */
$orderId = (int)($_POST['order_id'] ?? 0);
$newStatus = $validStatus($_POST['status'] ?? '');

if ($orderId <= 0 || $newStatus === null) {
    header("Location: " . $returnTo);
    exit();
}

$curStmt = $db->prepare("SELECT status FROM orders WHERE id=?");
$curStmt->bind_param("i", $orderId);
$curStmt->execute();
$current = $curStmt->get_result()->fetch_assoc();

if (!$current) {
    header("Location: " . $returnTo);
    exit();
}

$currentStatus = trim($current['status'] ?? 'Confirmed');
if (!isset($statusSteps[$currentStatus])) {
    $currentStatus = 'Confirmed';
}

if ($statusSteps[$newStatus] < $statusSteps[$currentStatus]) {
    die("Cannot move status backwards.");
}

if ($newStatus !== $currentStatus) {
    $upd = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $upd->bind_param("si", $newStatus, $orderId);
    $upd->execute();
}

header("Location: " . $returnTo);
exit();
