<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

$db = require __DIR__ . "/../config/config.php";

/* CSRF */
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $_SESSION['flash_error'] = "Invalid request (CSRF).";
    header("Location: dashboard.php?tab=customers");
    exit();
}

$customerId = (int)($_POST['id'] ?? 0);
if ($customerId <= 0) {
    $_SESSION['flash_error'] = "Invalid customer id.";
    header("Location: dashboard.php?tab=customers");
    exit();
}

$db->begin_transaction();

try {
    /* 1) Get customer email */
    $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();

    if (!$customer) {
        throw new Exception("Customer not found.");
    }

    $email = $customer['email'];

    /* 2) Delete order_items for this user's orders (JOIN delete) */
    $stmt = $db->prepare("
        DELETE oi
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE o.user_id = ?
    ");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();

    /* 3) Delete orders */
    $stmt = $db->prepare("DELETE FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();

    /* 4) Delete feedback_message */
    $stmt = $db->prepare("DELETE FROM feedback_message WHERE user_id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();

    /* 5) Delete contact_messages by email */
    $stmt = $db->prepare("DELETE FROM contact_messages WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    /* 6) Finally delete user */
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();

    $db->commit();

    $_SESSION['flash_success'] = "Customer and related records deleted successfully.";
    header("Location: dashboard.php?tab=customers");
    exit();

} catch (Exception $e) {
    $db->rollback();
    $_SESSION['flash_error'] = "Delete failed: " . $e->getMessage();
    header("Location: dashboard.php?tab=customers");
    exit();
}
