<?php

session_start();
if(!isset($_SESSION['email'])) {
    header('Location: index1.php');
    exit();
}

?>


