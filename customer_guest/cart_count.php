<?php
session_start();
include_once "dbcon.php";

$sid = session_id();

$stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(quantity),0) AS total_qty FROM cart_items WHERE session_id = ?");
mysqli_stmt_bind_param($stmt, "s", $sid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

echo json_encode(['total_qty' => (int)($row['total_qty'] ?? 0)]);
