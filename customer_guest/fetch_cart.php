<?php
session_start();
include_once "dbcon.php";

$sid = session_id();
$q = mysqli_query($con,"SELECT c.id, c.quantity, m.name, m.price FROM cart_items c JOIN menu_items m ON c.menu_id=m.id WHERE c.session_id='$sid'");
$items = [];
$total_qty = 0;
while($r=mysqli_fetch_assoc($q)){
    $items[] = $r;
    $total_qty += $r['quantity'];
}
echo json_encode(['items'=>$items,'total_qty'=>$total_qty]);
