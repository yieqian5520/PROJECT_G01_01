<?php   

session_start();
include_once __DIR__ . "/includes/dbcon.php";

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path as needed if you're not using Composer

function sendemail_verify($name,$email,$verify_token)
{
    
}

if(isset($_POST['register_btn']))
{
    $name = $_POST['name'];
    $name = $_POST['phone'];
    $name = $_POST['email'];
    $name = $_POST['password'];
    $name = $_POST['confirm_password'];
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