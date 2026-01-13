<?php
// add_to_cart.php
session_start();
include_once __DIR__ . "/dbcon.php";

// Grab values
$menu_id = (int)($_POST['menu_id'] ?? 0);
$qty_in  = (int)($_POST['quantity'] ?? 1);
$quantity = $qty_in > 0 ? $qty_in : 1;

$temp  = trim($_POST['temp'] ?? 'Hot');        // Hot/Cold
$milk  = trim($_POST['milk'] ?? '');           // Oat/Soy/Almond/Normal
$syrup = trim($_POST['syrup'] ?? '');          // Caramel/Hazelnut/Vanilla/None
$addonsArr = $_POST['addons'] ?? [];
$addons = is_array($addonsArr) ? implode(", ", $addonsArr) : "";

if ($menu_id <= 0) {
    header("Location: menu.php");
    exit();
}

$session_id = session_id();

// Verify menu item exists
$check = mysqli_prepare($con, "SELECT id FROM menu_items WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($check, "i", $menu_id);
mysqli_stmt_execute($check);
$checkRes = mysqli_stmt_get_result($check);

if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    die("Error: menu_id not found in menu_items table.");
}

/*
  NOTE:
  To save temp/milk/syrup/addons, ensure cart_items has these columns:
    temp VARCHAR(10), milk VARCHAR(30), syrup VARCHAR(30), addons TEXT
  If not, add them via ALTER TABLE (see section 4 below).
*/

// Insert as a new row with options
$ins = mysqli_prepare($con, "INSERT INTO cart_items 
    (session_id, menu_id, quantity, temp, milk, syrup, addons)
    VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$ins) {
    die("Insert prepare failed. Make sure columns exist. Error: " . mysqli_error($con));
}
mysqli_stmt_bind_param($ins, "siissss", $session_id, $menu_id, $quantity, $temp, $milk, $syrup, $addons);
mysqli_stmt_execute($ins);

header("Location: cart.php");
exit();