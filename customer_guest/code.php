<?php
session_start();
include_once __DIR__ . "/dbcon.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendemail_verify($name, $email, $verify_token)
{
    $mail = new PHPMailer(true);

    try {
        // --- SMTP Server Settings ---
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "bananacoffee06@gmail.com"; // Your Gmail
        $mail->Password   = "rfvo klcg vudy spra";         // Your 16-char App Password (NO SPACES)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- Localhost SSL Fix (Crucial for XAMPP) ---
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // --- Recipients ---
        $mail->setFrom("bananacoffee06@gmail.com", $name);
        $mail->addAddress($email);

        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = "Email Verification from Funda of Web IT";
        
        $email_template = "
            <h2>You have Registered with Funda of Web IT</h2>
            <h5>Verify your email address to Login with the link below:</h5>
            <br/><br/>
            <a href='http://localhost/Master%20Project%20-Pucks%20Coffee/PROJECT_G01_01/PROJECT_G01_01/customer_guest/verify-email.php?token=$verify_token'>Click Here to Verify</a>
        ";

        $mail->Body = $email_template;

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Logs the error to the screen if it fails
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if(isset($_POST['register_btn']))
{
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $verify_token = md5(rand());

     // Email  Exist or not
     $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
     $check_email_query_run = mysqli_query($con, $check_email_query);

     if(mysqli_num_rows($check_email_query_run) > 0)
     {
         $_SESSION['status'] = "Email Already Exists";
         header("Location: register.php");
     }
     else
     {
         // Insert User / Register User Data
         $query = "INSERT INTO users (name,phone,email,password,confirm_password,verify_token) VALUES ('$name','$phone','$email','$password','$confirm_password','$verify_token')";
         $query_run = mysqli_query($con, $query);

         if($query_run)
         { 
             sendemail_verify("$name", "$email", "$verify_token");

             $_SESSION['status'] = "Registration Successful. Please Verify Your Email Address.";
             header("Location: register.php");
         }
         else
         {
             $_SESSION['status'] = "Registration Failed";
             header("Location: register.php");
         }
     }
}
?>