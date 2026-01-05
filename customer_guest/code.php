<?php

if(isset($_POST['register_btn']))
{
    $name = $_POST['name'];
    $name = $_POST['phone'];
    $name = $_POST['email'];
    $name = $_POST['password'];
    $name = $_POST['confirm_password'];

    // Email  Exist or not
    $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_query_run = mysql_query($con, $check_email_query);

    if()
    {

    }
    else
        {

    }
}

?>