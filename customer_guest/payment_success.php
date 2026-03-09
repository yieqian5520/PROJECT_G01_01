<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
include_once "dbcon.php";

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/assets/vendor/stripe/stripe-php/init.php')) {
    require __DIR__ . '/assets/vendor/stripe/stripe-php/init.php';
} else {
    die("Stripe library not found.");
}

\Stripe\Stripe::setApiKey('sk_test_your_secret_key_here'); // replace with your real Stripe test secret key

if (!isset($_GET['session_id']) || !isset($_GET['order_id'])) {
    die("Invalid payment response.");
}

$session_id = $_GET['session_id'];
$order_id = (int)$_GET['order_id'];

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($session && $session->payment_status === 'paid') {
        $update = mysqli_query($con, "
            UPDATE orders
            SET payment_status = 'PAID',
                payment_method = 'Credit Card'
            WHERE id = $order_id
        ");

        if (!$update) {
            die("Failed to update payment status.");
        }

        $q = mysqli_query($con, "
            SELECT order_code
            FROM orders
            WHERE id = $order_id
            LIMIT 1
        ");

        $row = mysqli_fetch_assoc($q);

        if (!empty($row['order_code'])) {
            header("Location: order_status.php?order=" . urlencode($row['order_code']));
            exit;
        }

        die("Order code not found.");
    }

    echo "Payment not completed.";

} catch (Exception $e) {
    echo "Stripe verification failed: " . htmlspecialchars($e->getMessage());
}