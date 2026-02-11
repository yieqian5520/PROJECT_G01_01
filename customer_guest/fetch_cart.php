<?php
session_start();

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

include_once "dbcon.php";

$sid = session_id();

/**
 * If menu.php sends order_type, store it into session
 * so cart.php / other pages use the same order type.
 */
if (isset($_POST['order_type']) && $_POST['order_type'] !== '') {
    $_SESSION['order_type'] = $_POST['order_type'];
}

$type = $_SESSION['order_type'] ?? 'Dine In';

/**
 * Fetch cart items for this session + order type
 */
$sql = "
    SELECT 
        m.name,
        m.price,
        c.quantity
    FROM cart_items c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.session_id = ?
      AND c.order_type = ?
";

$stmt = $con->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'order_type' => $type,
        'items' => [],
        'total' => 0,
        'error' => 'Prepare failed: ' . $con->error
    ]);
    exit;
}

$stmt->bind_param("ss", $sid, $type);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
$total = 0.0;

while ($r = $res->fetch_assoc()) {
    $price = (float)$r['price'];
    $qty   = (int)$r['quantity'];
    $line  = $price * $qty;

    $items[] = [
        'name' => $r['name'],
        'price' => $price,
        'quantity' => $qty,
        'line' => $line
    ];

    $total += $line;
}

$stmt->close();

echo json_encode([
    'order_type' => $type,
    'items' => $items,
    'total' => $total
]);
exit;