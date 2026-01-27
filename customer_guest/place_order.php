<?php
session_start();
include_once "dbcon.php";

$user_id = $_SESSION['auth_user']['id'];
$sid = session_id();

$order_code = "PUCKS" . time();
$total = 0;

// Get cart
$cart = mysqli_query($con, "
    SELECT c.*, m.name, m.price
    FROM cart_items c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.session_id='$sid'
");

$items = [];
while ($row = mysqli_fetch_assoc($cart)) {
    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

// Insert order
mysqli_query($con, "
    INSERT INTO orders (user_id, order_code, total)
    VALUES ($user_id, '$order_code', $total)
");

$order_id = mysqli_insert_id($con);

// Insert order items
foreach ($items as $i) {
    mysqli_query($con, "
        INSERT INTO order_items 
        (order_id, menu_name, price, quantity, temp, milk, syrup, addons)
        VALUES (
          $order_id,
          '{$i['name']}',
          {$i['price']},
          {$i['quantity']},
          '{$i['temp']}',
          '{$i['milk']}',
          '{$i['syrup']}',
          '{$i['addons']}'
        )
    ");
}

// Clear cart
mysqli_query($con, "DELETE FROM cart_items WHERE session_id='$sid'");

header("Location: payment.php?order=$order_code");
exit;
