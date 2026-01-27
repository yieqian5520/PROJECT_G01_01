<?php
include_once "dbcon.php";

$order = $_GET['order'];

mysqli_query($con, "
    UPDATE orders 
    SET status='Paid'
    WHERE order_code='$order'
");

header("Location: order_status.php?order=$order");
exit;
