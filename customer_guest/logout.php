<?php
session_start();

// Remove authentication data
unset($_SESSION['authenticated']);
unset($_SESSION['auth_user']);

// Optional: destroy entire session (recommended)
session_destroy();

// Logout message
$_SESSION['status'] = "You have been logged out successfully.";

// Redirect to login page
header("Location: login.php");
exit();
