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

/* Selected customer IDs */
$ids = $_POST['ids'] ?? [];
if (!is_array($ids) || empty($ids)) {
    $_SESSION['flash_error'] = "No customers selected.";
    header("Location: dashboard.php?tab=customers");
    exit();
}

/* sanitize */
$ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($v) => $v > 0)));
if (empty($ids)) {
    $_SESSION['flash_error'] = "No valid customers selected.";
    header("Location: dashboard.php?tab=customers");
    exit();
}

$deletedUsers = 0;
$errors = [];

foreach ($ids as $customerId) {
    $db->begin_transaction();

    try {
        /* 1) Get customer email */
        $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();

        if (!$customer) {
            throw new Exception("Customer ID $customerId not found.");
        }
        $email = $customer['email'];

        /* 2) Delete order_items ONLY for UNPAID orders */
        $stmt = $db->prepare("
            DELETE oi
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE o.user_id = ?
              AND (o.payment_status IS NULL OR o.payment_status = 'UNPAID')
              AND o.paid_at IS NULL
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        /* 3) Delete orders ONLY for UNPAID orders */
        $stmt = $db->prepare("
            DELETE FROM orders
            WHERE user_id = ?
              AND (payment_status IS NULL OR payment_status = 'UNPAID')
              AND paid_at IS NULL
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        /* 4) Detach PAID orders (KEEP THEM) */
        $stmt = $db->prepare("
            UPDATE orders
            SET user_id = NULL
            WHERE user_id = ?
              AND (payment_status = 'PAID' OR paid_at IS NOT NULL)
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        /* 5) Delete feedback_message */
        $stmt = $db->prepare("DELETE FROM feedback_message WHERE user_id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        /* 6) Delete contact_messages by email */
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        /* 7) Finally delete user */
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        $db->commit();

        // Count only if the user row was actually deleted
        if ($stmt->affected_rows > 0) $deletedUsers++;

    } catch (Exception $e) {
        $db->rollback();
        $errors[] = "ID {$customerId}: " . $e->getMessage();
    }
}

/* flash message */
if ($deletedUsers > 0) {
    $_SESSION['flash_success'] = "Deleted {$deletedUsers} customer(s). UNPAID orders removed; PAID orders kept.";
}

if (!empty($errors)) {
    // keep it short (avoid huge session message)
    $_SESSION['flash_error'] = "Some deletions failed: " . implode(" | ", array_slice($errors, 0, 5));
}

header("Location: dashboard.php?tab=customers");
exit();
