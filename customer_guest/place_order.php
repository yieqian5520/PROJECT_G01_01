<?php
session_start();
include_once "dbcon.php";

if (!isset($_SESSION['auth_user'])) {
    // Redirect to login if user not logged in
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['auth_user']['id'];
$sid = session_id();
$type = $_SESSION['order_type'] ?? 'Dine In';

$order_code = "PUCKS" . date("YmdHis") . rand(100,999);
$total = 0;

// Get cart items
$cart = mysqli_query($con, "
    SELECT c.*, m.name, m.price, m.image
    FROM cart_items c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.session_id='$sid'
");

$items = [];
while ($row = mysqli_fetch_assoc($cart)) {
    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

if (empty($items)) {
    // If cart is empty, redirect back to cart
    header("Location: cart.php");
    exit;
}

$insertOrder = mysqli_query($con, "
    INSERT INTO orders (user_id, order_code, total, status, created_at)
    VALUES ($user_id, '$order_code', $total, 'Confirmed', NOW())
");

if (!$insertOrder) {
    die("ORDER INSERT FAILED: " . mysqli_error($con));
}

$order_id = mysqli_insert_id($con);

// Insert order items
foreach ($items as $i) {
    $menu_name = mysqli_real_escape_string($con, $i['name']);
    $price = (float)$i['price'];
    $qty = (int)$i['quantity'];

    $temp  = mysqli_real_escape_string($con, $i['temp'] ?? '');
    $milk  = mysqli_real_escape_string($con, $i['milk'] ?? '');
    $syrup = mysqli_real_escape_string($con, $i['syrup'] ?? '');
    $addons= mysqli_real_escape_string($con, $i['addons'] ?? '');

    $ok = mysqli_query($con, "
        INSERT INTO order_items (order_id, menu_name, price, quantity, temp, milk, syrup, addons)
        VALUES ($order_id, '$menu_name', $price, $qty, '$temp', '$milk', '$syrup', '$addons')
    ");

    if (!$ok) {
        die("ORDER ITEM INSERT FAILED: " . mysqli_error($con));
    }
}


// Clear cart
mysqli_query($con,"DELETE FROM cart_items WHERE session_id='$sid'");
unset($_SESSION['order_type']);

header("Location: order_status.php?latest=1");
exit;


