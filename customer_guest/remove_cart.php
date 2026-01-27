<?php
session_start();
include_once "dbcon.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sid = session_id();

    mysqli_query($con,
        "DELETE FROM cart_items 
         WHERE id=$id AND session_id='$sid'"
    );
}

header("Location: cart.php");
exit;
