<?php
session_start();

if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true)
{
    $_SESSION['status'] = "Please Login to Access User Dashboard.";
    header("Location: login.php");
    exit();
}
?>
