<?php
session_start();
include_once __DIR__ . "/dbcon.php";

if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_SESSION['status'] = "Not allowed.";
    header("Location: login.php");
    exit();
}

$token = mysqli_real_escape_string($con, $_GET['token']);

$verify_query = "SELECT verify_token, verify_status FROM users WHERE verify_token='$token' LIMIT 1";
$verify_query_run = mysqli_query($con, $verify_query);

if (mysqli_num_rows($verify_query_run) == 0) {
    $_SESSION['status'] = "This token does not exist.";
    header("Location: login.php");
    exit();
}

$row = mysqli_fetch_assoc($verify_query_run);

if ($row['verify_status'] == "1") {
    $_SESSION['status'] = "Account already verified. Please login.";
    header("Location: login.php");
    exit();
}

// update verify_status to 1
$update_query = "UPDATE users SET verify_status='1' WHERE verify_token='$token' LIMIT 1";
$update_run = mysqli_query($con, $update_query);

$_SESSION['status'] = $update_run
    ? "Your account has been verified successfully!"
    : "Verification failed! Please try again.";

header("Location: login.php");
exit();
?>
