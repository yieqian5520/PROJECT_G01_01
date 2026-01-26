<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

$db = require __DIR__ . "/../config/config.php";

// CSRF check
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    die("Invalid CSRF token");
}

$customerId = (int)($_POST['id'] ?? 0);
if ($customerId <= 0) { die("Invalid customer id"); }

$db->begin_transaction();

try {
    // 1) get customer email (needed to delete contact_messages)
    $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $res = $stmt->get_result();
    $customer = $res->fetch_assoc();

    if (!$customer) {
        throw new Exception("Customer not found.");
    }

    $email = $customer['email'];

    // 2) delete related contact messages by email
    $stmt = $db->prepare("DELETE FROM contact_messages WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // 3) delete customer
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();

    $db->commit();
    header("Location: staff_dashboard.php?tab=customers");
    exit();

} catch (Exception $e) {
    $db->rollback();
    die("Delete failed: " . $e->getMessage());
}
