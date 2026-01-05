<?php   

session_start();
include_once __DIR__ . "/includes/dbcon.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

function sendemail_verify($name,$email,$verify_token)
{
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = 2;

    $mail->isSMTP();
    $mail->SMTPAuth = true;
    
    $mail->Host = "smtp.gmail.com";
    $mail->Username = "favian@gmail.com";
    $mail->Password = "favian@12345";
    
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    
    $mail->setFrom("favian@gmail.com", $name);
    $mail->addAddress($email);
    
    $mail->isHTML(true);
    $mail->Subject = "Email Verification from Funda of Web IT";
    
    $email_template = "
    <h2>You have Registered with Funda of Web IT</h2>
    <h5>Verify your email address to Login with the below given link</h5>
    <br/><br/>
    <a href='http://localhost/fundaofwebit/register-login-with-verification/verify-email.php?token=$verify_token'>Click Me</a>
    ";
    
    $mail->Body = $email_template;
    $mail->send();

    $mail->Body = $email_template;
    $mail->send();
    echo 'Message has been sent';
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