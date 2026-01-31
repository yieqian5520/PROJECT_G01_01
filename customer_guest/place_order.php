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

$order_code = "PUCKS" . time();
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

// Insert order
mysqli_query($con, "
    INSERT INTO orders (user_id, order_code, total_amount, order_type, status)
    VALUES ($user_id, '$order_code', $total, '$type', 'Confirmed')
");

$order_id = mysqli_insert_id($con);

// Insert order items
foreach ($items as $i) {
    mysqli_query($con, "
        INSERT INTO order_items 
        (order_id, item_name, item_image, price, quantity, temp, milk, syrup, addons)
        VALUES (
          $order_id,
          '".mysqli_real_escape_string($con, $i['name'])."',
          '".mysqli_real_escape_string($con, $i['image'])."',
          {$i['price']},
          {$i['quantity']},
          '".($i['temp'] ?? '')."',
          '".($i['milk'] ?? '')."',
          '".($i['syrup'] ?? '')."',
          '".($i['addons'] ?? '')."'
        )
    ");
}

// Clear cart
mysqli_query($con,"DELETE FROM cart_items WHERE session_id='$sid'");
unset($_SESSION['order_type']);

echo "<script>
    window.top.location.href = 'order_status.php?order=$order_code';
</script>";
exit;
