<?php
session_start();
include_once "dbcon.php";

$sid = session_id();
$q = mysqli_query($con,"SELECT SUM(quantity) as total FROM cart_items WHERE session_id='$sid'");
$row = mysqli_fetch_assoc($q);
$total_qty = $row['total'] ?? 0;
echo json_encode(['total_qty'=>$total_qty]);
