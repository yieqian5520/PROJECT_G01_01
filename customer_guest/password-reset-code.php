<?php
session_start();
include_once __DIR__ . "/dbcon.php";

if(isset($_POST['password_reset_link']))
{
    $email = mysqli_real_escape_string($con,$_POST['email']);
    $token = md5(rand());

    $check_email = "SELESCT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_run = mysqli_query($con, $check_email);

    if(mysqli_num_rows($check_email_run) > 0)
    {
        $row = mysqli_fetch_array($check_email_run);
        $get_name = $row['name'];
        $get_email = $row['email'];

        $update_token = "UPDATE users SET verify_token='$token' WHERE email='$get_email' LIMIT 1";
        $update_token_run = mysqli_query($con, $update_token);

        if($update_token_run)
        {
            send_password_reset($get_name, $get_email, $token);
            $_SESSION['status'] = "We Emailed You a Password Reset Link";
            header("Location: password-reset.php");
            exit(0);
        }
        else
        {
            $_SESSION['status'] = "Something Went Wrong. Please Try Again.";
            header("Location: password-reset.php");
            exit(0);
        }
    }
    else
    {
        $_SESSION['status'] = "Email Not Found";
        header("Location: password-reset.php");
        exit(0);
    }
}