<?php
session_start();
include_once __DIR__ . "/dbcon.php";

if (isset($_POST['login_now_btn'])) {

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

    if ($row['verify_status'] != "1") {
        $_SESSION['status'] = "Please verify your email address to login.";
        header("Location: login.php");
        exit();
    }

    $dbPass = $row['password'];

    // ✅ Support both hashed and old plain text
    $isHashed = str_starts_with($dbPass, '$2y$') || str_starts_with($dbPass, '$argon2');
    $valid = $isHashed ? password_verify($password, $dbPass) : ($password === $dbPass);

    if (!$valid) {
        $_SESSION['status'] = "Invalid Email or Password.";
        header("Location: login.php");
        exit();
    }

    // If old plain-text, upgrade to hash automatically
    if (!$isHashed) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $uid = (int)$row['id'];
        mysqli_query($con, "UPDATE users SET password='$newHash' WHERE id=$uid LIMIT 1");
        $row['password'] = $newHash;
    }

    $_SESSION['authenticated'] = true;
    $_SESSION['auth_user'] = [
        'id'            => $row['id'],
        'username'      => $row['name'],
        'email'         => $row['email'],
        'phone'         => $row['phone'],
        'address'       => $row['address'],
        'profile_image' => $row['profile_image'],
    ];

    $_SESSION['status'] = "You are Logged In Successfully.";
    header("Location: dashboard.php");
    exit();
}

header("Location: login.php");
exit();
?>
