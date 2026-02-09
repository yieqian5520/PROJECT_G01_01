<?php
session_start();
include_once "dbcon.php";

$sid  = session_id();
$type = $_SESSION['order_type'] ?? 'Dine In';

$response = [
  'status' => 'ok',
  'total'  => 0,
  'items'  => []
];

if(!isset($_POST['action'])) {
  echo json_encode($response);
  exit;
}

$id = intval($_POST['id']);

if($_POST['action'] === 'update'){
  $qty = max(0, intval($_POST['qty']));

  if($qty == 0){
    mysqli_query($con,"DELETE FROM cart_items WHERE id=$id");
  }else{
    mysqli_query($con,"UPDATE cart_items SET quantity=$qty WHERE id=$id");
  }
}

if($_POST['action'] === 'remove'){
  mysqli_query($con,"DELETE FROM cart_items WHERE id=$id");
}

/* Recalculate cart */
$q = mysqli_query($con,"
SELECT c.id, c.quantity, m.price
FROM cart_items c
JOIN menu_items m ON c.menu_id=m.id
WHERE c.session_id='$sid'
AND c.order_type='$type'
");

while($r=mysqli_fetch_assoc($q)){
  $line = $r['price'] * $r['quantity'];
  $response['total'] += $line;
  $response['items'][$r['id']] = [
    'qty'  => $r['quantity'],
    'line' => number_format($line,2)
  ];
}

echo json_encode($response);
