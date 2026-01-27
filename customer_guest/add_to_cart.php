<?php
session_start();
include_once "dbcon.php";

if (isset($_POST['menu_id'])) {

    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['quantity'] ?? 1;
    $temp = $_POST['temp'] ?? 'Hot';
    $milk = $_POST['milk'] ?? '';
    $syrup = $_POST['syrup'] ?? '';
    $addons = $_POST['addons'] ?? '';

    $sid = session_id();

    // Check if item already exists
    $check = mysqli_prepare($con,
        "SELECT id FROM cart_items 
         WHERE session_id=? AND menu_id=? AND temp=? AND milk=? AND syrup=?"
    );

    mysqli_stmt_bind_param($check, "sisss", $sid, $menu_id, $temp, $milk, $syrup);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        // Update quantity
        mysqli_query($con,
            "UPDATE cart_items 
             SET quantity = quantity + $quantity 
             WHERE session_id='$sid' AND menu_id=$menu_id"
        );
    } else {
        // Insert new item
        $stmt = mysqli_prepare($con,
            "INSERT INTO cart_items 
            (session_id, menu_id, quantity, temp, milk, syrup, addons)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "siissss",
            $sid,
            $menu_id,
            $quantity,
            $temp,
            $milk,
            $syrup,
            $addons
        );

        mysqli_stmt_execute($stmt);
    }

    header("Location: cart.php");
    exit;
}
