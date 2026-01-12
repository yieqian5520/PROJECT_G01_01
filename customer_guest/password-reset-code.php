<?php
session_start();
include_once __DIR__ . "/dbcon.php";
include_once __DIR__ . "/mail_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_password_reset($name, $email, $token)
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

        $link = BASE_URL . "/password-change.php?token=" . urlencode($token) . "&email=" . urlencode($email);

        $mail->isHTML(true);
        $mail->Subject = "Password Reset - Pucks Coffee";
        $mail->Body = "
            <h2>Hello {$name}</h2>
            <p>Click the link below to reset your password:</p>
            <p><a href='{$link}'>Reset Password</a></p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Reset Mail Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Send reset link
if(isset($_POST['password_reset_link']))
{
    $email = trim($_POST['email'] ?? '');
    if ($email === '') {
        $_SESSION['status'] = "Please enter your email.";
        header("Location: password-reset.php");
        exit();
    }

    $email = mysqli_real_escape_string($con, $email);
    $token = md5(rand());

    $q = "SELECT name, email FROM users WHERE email='$email' LIMIT 1";
    $r = mysqli_query($con, $q);

    if (mysqli_num_rows($r) === 0) {
        $_SESSION['status'] = "Email Not Found";
        header("Location: password-reset.php");
        exit();
    }

    $row = mysqli_fetch_assoc($r);

    // store token in verify_token (you already use this column)
    $upd = mysqli_query($con, "UPDATE users SET verify_token='$token' WHERE email='$email' LIMIT 1");

    if ($upd) {
        $sent = send_password_reset($row['name'], $row['email'], $token);
        $_SESSION['status'] = $sent ? "We emailed you a reset link." : "Token saved, but email failed to send.";
        header("Location: password-reset.php");
        exit();
    }

    $_SESSION['status'] = "Something went wrong. Please try again.";
    header("Location: password-reset.php");
    exit();
}

// Update password
if(isset($_POST['password_update']))
{
    $email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $token = mysqli_real_escape_string($con, $_POST['password_token'] ?? '');

    if ($token === '' || $email === '' || $new_password === '' || $confirm_password === '') {
        $_SESSION['status'] = "All fields are mandatory.";
        header("Location: password-change.php?token=$token&email=$email");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['status'] = "Password and Confirm Password do not match.";
        header("Location: password-change.php?token=$token&email=$email");
        exit();
    }

    $check = mysqli_query($con, "SELECT id FROM users WHERE verify_token='$token' AND email='$email' LIMIT 1");
    if (mysqli_num_rows($check) === 0) {
        $_SESSION['status'] = "Invalid token.";
        header("Location: password-change.php?token=$token&email=$email");
        exit();
    }

    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    $new_token = md5(rand())."fund";

    $ok = mysqli_query($con, "UPDATE users SET password='$hash', verify_token='$new_token' WHERE verify_token='$token' AND email='$email' LIMIT 1");

    $_SESSION['status'] = $ok ? "New Password Updated Successfully." : "Password update failed.";
    header("Location: login.php");
    exit();
}
?>
