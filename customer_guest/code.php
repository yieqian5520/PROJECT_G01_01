<?php
session_start();
include_once __DIR__ . "/dbcon.php";

if(isset($_POST['register_btn']))
{
    $name  = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Password match check
    if ($password !== $confirm) {
        $_SESSION['status'] = "Password and Confirm Password do not match.";
        header("Location: register.php");
        exit();
    }

    // Password requirement: at least 8 chars, 1 letter, 1 number, 1 special
    if(!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/", $password)) {
        $_SESSION['status'] = "Password must be at least 8 characters, include a letter, a number, and a special character.";
        header("Location: register.php");
        exit();
    }

    // Check existing email
    $check = mysqli_prepare($con, "SELECT id FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $_SESSION['status'] = "Email Already Exists.";
        header("Location: register.php");
        exit();
    }
    mysqli_stmt_close($check);

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($con, "INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $email, $password_hash);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['status'] = "Registration Successful. You can now login.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['status'] = "Registration Failed: " . mysqli_error($con);
        header("Location: register.php");
        exit();
    }
}
?>
