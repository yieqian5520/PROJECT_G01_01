<?php
session_start();
include_once __DIR__ . "/dbcon.php";

$menu_id = (int)($_GET['menu_id'] ?? 0);
if ($menu_id <= 0) {
    header("Location: menu.php");
    exit();
}

$session_id = session_id();

/*
  IMPORTANT:
  If menu_items has no record with this id, we should stop.
  Otherwise you can add invalid ids and cart JOIN will show nothing.
*/
$check = mysqli_prepare($con, "SELECT id FROM menu_items WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($check, "i", $menu_id);
mysqli_stmt_execute($check);
$checkRes = mysqli_stmt_get_result($check);

if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    die("Error: menu_id not found in menu_items table. Please insert the menu items first.");
}

// check if already in cart for this session
$stmt = mysqli_prepare($con, "SELECT id, quantity FROM cart_items WHERE session_id=? AND menu_id=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "si", $session_id, $menu_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($res)) {
    $cart_id = (int)$row['id'];
    $new_qty = (int)$row['quantity'] + 1;

    $upd = mysqli_prepare($con, "UPDATE cart_items SET quantity=? WHERE id=? AND session_id=?");
    mysqli_stmt_bind_param($upd, "iis", $new_qty, $cart_id, $session_id);
    mysqli_stmt_execute($upd);
} else {
    $qty = 1;
    $ins = mysqli_prepare($con, "INSERT INTO cart_items (session_id, menu_id, quantity) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($ins, "sii", $session_id, $menu_id, $qty);
    mysqli_stmt_execute($ins);
}

header("Location: cart.php");
exit();