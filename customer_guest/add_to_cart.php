<?php
session_start();
include_once "dbcon.php";

if(!isset($_POST['menu_id']) || !isset($_POST['quantity'])){
    echo json_encode(['status'=>'error','msg'=>'Invalid data']);
    exit;
}

$menu_id = intval($_POST['menu_id']);
$quantity = intval($_POST['quantity']);
$option = $_POST['temp'] ?? '';
$addons = isset($_POST['addons']) ? implode(',', $_POST['addons']) : '';
$milk = $_POST['milk'] ?? '';
$syrup = $_POST['syrup'] ?? '';
$sid = session_id();

// Check if item exists
$q = mysqli_query($con, "SELECT * FROM cart_items WHERE session_id='$sid' AND menu_id=$menu_id LIMIT 1");
if(mysqli_num_rows($q)){
    mysqli_query($con,"UPDATE cart_items SET quantity=quantity+$quantity WHERE session_id='$sid' AND menu_id=$menu_id");
}else{
    mysqli_query($con,"INSERT INTO cart_items (session_id, menu_id, quantity, temp, addons, milk, syrup)
        VALUES ('$sid',$menu_id,$quantity,'$option','$addons','$milk','$syrup')");
}

echo json_encode(['status'=>'success']);
