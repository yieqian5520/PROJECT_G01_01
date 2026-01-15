<?php
session_start();
include_once __DIR__ . "/dbcon.php";

if (!isset($_POST['login_now_btn'])) {
    header("Location: login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['status'] = "All fields are mandatory.";
    header("Location: login.php");
    exit();
}

$email = mysqli_real_escape_string($con, $email);

$login_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
$login_query_run = mysqli_query($con, $login_query);

if (mysqli_num_rows($login_query_run) === 0) {
    $_SESSION['status'] = "Invalid Email or Password.";
    header("Location: login.php");
    exit();
}

$row = mysqli_fetch_assoc($login_query_run);

// Check if email is verified
if ($row['verify_status'] != "1") {
    $_SESSION['status'] = "Please verify your email address to login.";
    header("Location: login.php");
    exit();
}

// Verify password (hashed)
if (!password_verify($password, $row['password'])) {
    $_SESSION['status'] = "Invalid Email or Password.";
    header("Location: login.php");
    exit();
}

// Set session
$_SESSION['authenticated'] = true;
$_SESSION['auth_user'] = [
    'id'       => $row['id'],
    'username' => $row['name'],
    'email'    => $row['email'],
    'phone'    => $row['phone'],
    'address'  => $row['address'],
    'profile_image' => $row['profile_image'],
];

// Redirect to dashboard
header("Location: dashboard.php");
exit();
?>
