<?php
$con = mysqli_connect("localhost", "root", "", "pucks_coffee");

if (!$con) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
mysqli_set_charset($con, "utf8mb4");
?>