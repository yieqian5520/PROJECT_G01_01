<?php

$con =mysqli_connect("localhost","root","","pucks_coffee");

if ($con->connect_error){
    die("Connection failed: " . $con->connect_error);
}
?>