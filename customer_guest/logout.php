<?php
session_start();
include_once __DIR__ . "/dbcon.php";

// If customer is logged in, clear their cart
if (isset($_SESSION['authenticated'])) {
    $sid = session_id();

    if (!empty($sid)) {
        $stmt = mysqli_prepare($con, "DELETE FROM cart_items WHERE session_id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $sid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

// Optional message
$_SESSION['status'] = "You have been logged out successfully.";

// Remove auth data
unset($_SESSION['authenticated']);
unset($_SESSION['auth_user']);

// Destroy session completely
session_unset();
session_destroy();

// Redirect to login
header("Location: login.php");
exit();
?>