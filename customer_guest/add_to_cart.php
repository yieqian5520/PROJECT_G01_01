<?php
session_start();
include_once "dbcon.php";

$sid = session_id();

$menu_id   = intval($_POST['menu_id']);
$qty       = intval($_POST['quantity']);
$orderType = $_POST['order_type'] ?? 'Dine In';

$temp   = $_POST['temp'] ?? '';
$milk   = $_POST['milk'] ?? '';
$syrup  = $_POST['syrup'] ?? '';
$addons = isset($_POST['addons']) ? implode(',', $_POST['addons']) : '';

$q = mysqli_query($con,"
  SELECT id, quantity FROM cart_items
  WHERE session_id='$sid'
  AND menu_id=$menu_id
  AND order_type='$orderType'
");

if(mysqli_num_rows($q)){
  $r = mysqli_fetch_assoc($q);
  $newQty = $r['quantity'] + $qty;
  mysqli_query($con,"
    UPDATE cart_items
    SET quantity=$newQty
    WHERE id={$r['id']}
  ");
}else{
  mysqli_query($con,"
    INSERT INTO cart_items
    (session_id, menu_id, quantity, order_type, temp, milk, syrup, addons)
    VALUES
    ('$sid',$menu_id,$qty,'$orderType','$temp','$milk','$syrup','$addons')
  ");
}

echo json_encode(['status'=>'success']);
