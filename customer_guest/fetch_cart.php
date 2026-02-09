<?php
session_start();
include_once "dbcon.php";

$sid = session_id();
$type = $_SESSION['order_type'] ?? 'Dine In';

$q = mysqli_query($con,"
SELECT m.name, m.price, c.quantity
FROM cart_items c
JOIN menu_items m ON c.menu_id=m.id
WHERE c.session_id='$sid'
AND c.order_type='$type'
");

$items=[];
$total=0;

while($r=mysqli_fetch_assoc($q)){
  $items[]=$r;
  $total += $r['price']*$r['quantity'];
}

echo json_encode([
  'items'=>$items,
  'total'=>$total
]);
