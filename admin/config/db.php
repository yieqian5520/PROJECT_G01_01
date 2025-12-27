<?php
$host = "localhost";
$dbname = "pucks_coffee";
$user = "root";
$pass = ""; 

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_errno) {
  die("DB connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
