<?php
session_start();

$_SESSION['status'] = "You have been logged out successfully.";

unset($_SESSION['authenticated']);
unset($_SESSION['auth_user']);

header("Location: login.php");
exit();
?>
