<?php
session_start();
include_once __DIR__ . "/dbcon.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function resend_email_verify($name, $email, $verify_token)
{
    $mail = new PHPMailer(true);

    try {
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
        $mail->setFrom("bananacoffee06@gmail.com", "Pucks Coffee");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Resend - Email Verification";

        $mail->Body = "
            <h2>You have registered with Pucks Coffee</h2>
            <p>Please verify your email address:</p>
            <a href='http://localhost/Master%20Project%20-Pucks%20Coffee/PROJECT_G01_01/PROJECT_G01_01/customer_guest/verify-email.php?token=$verify_token'>
                Click Here to Verify
            </a>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

// ================= RESEND BUTTON =================

if (isset($_POST['resend_email_verify_btn'])) {

    if (!empty(trim($_POST['email']))) {

        $email = mysqli_real_escape_string($con, $_POST['email']);

        $checkemail_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $checkemail_query_run = mysqli_query($con, $checkemail_query);

        if (mysqli_num_rows($checkemail_query_run) > 0) {

            $row = mysqli_fetch_assoc($checkemail_query_run);

            if ($row['verify_status'] == "0") {

                $sent = resend_email_verify(
                    $row['name'],
                    $row['email'],
                    $row['verify_token']
                );

                if ($sent) {
                    $_SESSION['status'] = "Verification email resent successfully.";
                } else {
                    $_SESSION['status'] = "Failed to send verification email. Please try again later.";
                    error_log("Resend verification email failed for: " . $row['email']);
                }

                header("Location: login.php");
                exit();

            } else {
                $_SESSION['status'] = "Email already verified. Please login.";
                header("Location: login.php");
                exit();
            }

        } else {
            $_SESSION['status'] = "Email not registered. Please register.";
            header("Location: register.php");
            exit();
        }

    } else {
        $_SESSION['status'] = "Please enter your email.";
        header("Location: resend-email-verification.php");
        exit();
    }
}
?>
