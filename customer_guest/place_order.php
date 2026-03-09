<?php
session_start();
include_once "dbcon.php";

// 1️⃣ Check if user is logged in
if (!isset($_SESSION['auth_user'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['auth_user']['id'];
$sid = session_id();

// 2️⃣ Fetch ALL cart items (both Dine In + Take Away)
$cart_q = mysqli_query($con, "
    SELECT c.*, m.name, m.price, m.image
    FROM cart_items c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.session_id = '$sid'
    ORDER BY c.id ASC
");

if (!$cart_q) {
    die("Cart query failed: " . mysqli_error($con));
}

$items = [];
$total = 0.0;

while ($r = mysqli_fetch_assoc($cart_q)) {
    $line = (float)$r['price'] * (int)$r['quantity'];
    $total += $line;
    $items[] = $r;
}

// 3️⃣ If cart is empty, redirect back
if (empty($items)) {
    header("Location: cart.php");
    exit;
}

// 4️⃣ Generate unique order code
$order_code = "PUCKS" . date("YmdHis") . rand(100, 999);

// 5️⃣ Insert one order header
$stmt = $con->prepare("
    INSERT INTO orders (
        user_id, order_code, total, `status`, created_at, payment_status, payment_method
    )
    VALUES (?, ?, ?, 'Confirmed', NOW(), 'UNPAID', '')
");

if (!$stmt) {
    die("Prepare failed: " . $con->error);
}

$stmt->bind_param("isd", $user_id, $order_code, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// 6️⃣ Insert order items with EACH item's order_type
$stmt = $con->prepare("
    INSERT INTO order_items (
        order_id, menu_name, price, quantity, temp, milk, syrup, addons, order_type
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("Prepare failed (order_items): " . $con->error);
}

foreach ($items as $i) {
    $name       = $i['name'];
    $price      = (float)$i['price'];
    $qty        = (int)$i['quantity'];
    $temp       = $i['temp'] ?? '';
    $milk       = $i['milk'] ?? '';
    $syrup      = $i['syrup'] ?? '';
    $addons     = $i['addons'] ?? '';
    $order_type = $i['order_type'] ?? 'Dine In';

    $stmt->bind_param(
        "isidsssss",
        $order_id,
        $name,
        $price,
        $qty,
        $temp,
        $milk,
        $syrup,
        $addons,
        $order_type
    );
    $stmt->execute();
}
$stmt->close();

// 7️⃣ Clear all cart items for this session
mysqli_query($con, "DELETE FROM cart_items WHERE session_id = '$sid'");

// 8️⃣ Redirect to latest order
header("Location: order_status.php?latest=1");
exit;