<?php
session_start();
include_once __DIR__ . "/dbcon.php";
include_once __DIR__ . "/mail_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function resend_email_verify($name, $email, $verify_token)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);

        $verify_link = BASE_URL . "/verify-email.php?token=" . urlencode($verify_token);

        $mail->isHTML(true);
        $mail->Subject = "Resend Verification - Pucks Coffee";
        $mail->Body = "
            <h2>Hi {$name}</h2>
            <p>Please verify your email:</p>
            <p><a href='{$verify_link}'>Click Here to Verify</a></p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Resend Mail Error: {$mail->ErrorInfo}");
        return false;
    }
}

if (isset($_POST['resend_email_verify_btn'])) {

    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $_SESSION['status'] = "Please enter your email.";
        header("Location: resend-email-verification.php");
        exit();
    }

    $email = mysqli_real_escape_string($con, $email);

    $q = "SELECT name, email, verify_token, verify_status FROM users WHERE email='$email' LIMIT 1";
    $r = mysqli_query($con, $q);

    if (mysqli_num_rows($r) === 0) {
        $_SESSION['status'] = "Email not registered. Please register.";
        header("Location: register.php");
        exit();
    }

    $row = mysqli_fetch_assoc($r);

    if ($row['verify_status'] == "1") {
        $_SESSION['status'] = "Email already verified. Please login.";
        header("Location: login.php");
        exit();
    }

    $sent = resend_email_verify($row['name'], $row['email'], $row['verify_token']);

    $_SESSION['status'] = $sent
        ? "Verification email resent successfully."
        : "Failed to send email. Please try again later.";

    header("Location: login.php");
    exit();
}
?>
