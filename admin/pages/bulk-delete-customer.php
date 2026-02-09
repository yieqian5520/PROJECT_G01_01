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
        if (!$customer) throw new Exception("Customer ID $customerId not found.");
        $email = $customer['email'];

        /**
         * 2) SAVE PAID AMOUNTS INTO dashboard_daily_totals (group by day)
         *    (No customer/order data saved — only totals)
         */
        $paidStmt = $db->prepare("
            SELECT
              DATE(o.created_at) AS day,
              COALESCE(SUM(o.total),0) AS sales,
              COALESCE(SUM(GREATEST(oi.price - 4, 0) * oi.quantity),0) AS expenses
            FROM orders o
            JOIN order_items oi ON oi.order_id = o.id
            WHERE o.user_id = ?
              AND UPPER(TRIM(o.payment_status)) = 'PAID'
            GROUP BY DATE(o.created_at)
        ");
        $paidStmt->bind_param("i", $customerId);
        $paidStmt->execute();
        $paidRes = $paidStmt->get_result();

        $upsert = $db->prepare("
            INSERT INTO dashboard_daily_totals (day, sales, expenses, income)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              sales    = sales + VALUES(sales),
              expenses = expenses + VALUES(expenses),
              income   = income + VALUES(income)
        ");

        while ($row = $paidRes->fetch_assoc()) {
            $day = $row['day'];
            $sales = (float)$row['sales'];
            $expenses = (float)$row['expenses'];
            $income = $sales - $expenses;

            $upsert->bind_param("sddd", $day, $sales, $expenses, $income);
            $upsert->execute();
        }

        /**
         * 3) DELETE ALL order_items for this customer (paid/unpaid)
         *    We must delete items first due to FK constraints.
         */
        $stmt = $db->prepare("
            DELETE oi
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE o.user_id = ?
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        /**
         * 4) DELETE ALL orders for this customer (paid/unpaid)
         */
        $stmt = $db->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        /**
         * 5) DELETE feedback
         */
        $stmt = $db->prepare("DELETE FROM feedback_message WHERE user_id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        /**
         * 6) DELETE contact messages by email
         */
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        /**
         * 7) DELETE the customer
         */
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();

        $db->commit();

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
