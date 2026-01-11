<?php
session_start();
include_once __DIR__ . "/dbcon.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_password_reset($get_name, $get_email, $token)
{
     $mail = new PHPMailer(true);

    {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "bananacoffee06@gmail.com";
        $mail->Password   = "bmvzawwmlimtiqou"; // App password (no spaces)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // SSL fix for XAMPP
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Email content
        $mail->setFrom("bananacoffee06@gmail.com", $get_name);
        $mail->addAddress($get_email);

        $mail->isHTML(true);
        $mail->Subject = "Resend Password Notification";

        $mail->Body = "
            <h2>Hello</h2>
            <h3>You are receiving this email because we received a password reset request for your account.</h3>
            <a href='http://localhost/Master%20Project%20-Pucks%20Coffee/PROJECT_G01_01/PROJECT_G01_01/customer_guest/password-change.php?token=$token&email=$get_email'>
                Click Here to Verify
            </a>
        ";

        $mail->send();
        return true;

    }
}


if(isset($_POST['password_reset_link']))
{
    $email = mysqli_real_escape_string($con,$_POST['email']);
    $token = md5(rand());

    $check_email = "SELECT email FROM users WHERE email='$email' LIMIT 1";
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


if(isset($_POST['password_update']))
    {
        $email = mysqli_real_escape_string($con,$_POST['email']);
        $new_password = mysqli_real_escape_string($con,$_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($con,$_POST['confirm_password']);

        $token = mysqli_real_escape_string($con,$_POST['password_token']);

        if(!empty($token))
        {
                if(!empty($email) && !empty($new_password) && !empty($cnfirm_password))
                {
                    // Check if token is valid
                    $check_token = "SELECT verify_token FROM users WHERE verify_token='$token' LIMIT 1";
                    $check_token_run = mysqli_query($con,$check_token);

                    if(mysqli_num_rows($check_token_run) > 0)
                    {
                        if($new_password == $confirm_password)
                        {
                            // Update password
                            $update_password = "UPDATE users SET password=' $new_password' WHERE verify_token='$token' LIMIT 1";
                            $update_password_run = mysqli_query($con, $update_password);

                            if($update_password_run)
                            {
                                $_SESSION['status'] = "New Password Has Been Updated Successfully";
                                header("Location: login.php");
                                exit(0);
                            }
                            else
                            {
                                $_SESSION['status'] = "Did Not Update Password. Something Went Wrong.";
                                header("Location: password-change.php?token=$token&email=$email");
                                exit(0);
                            }
                        }
                        else
                        {
                            $_SESSION['status'] = "Password and Confirm Password Does Not Match";
                            header("Location: password-change.php");
                            exit(0);
                        }
                    }
                    else
                    {
                        $_SESSION['status'] = "Invalid Token";
                        header("Location: password-change.php?token&email=$email");
                        exit(0);
                    }
                }
                else
                {
                    $_SESSION['status'] = "All Filed are Mandatory";
                    header("Location: password-change.php?token&email=$email");
                    exit(0);
               }
        }
        else
        {
            $_SESSION['status'] = "No Token Available";
            header("Location:password-change.php");
            exit(0);
        }
    }


?>