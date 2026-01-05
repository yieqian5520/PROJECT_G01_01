<?php
session_start();

if(isset($_SESSION['authenticated']))
{
    header("Location: dashboard.php");
    exit(0);
} 

?>