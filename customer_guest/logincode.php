<?php
session_start();
include_once __DIR__ . "/dbcon.php";

if (isset($_POST['login_now_btn'])) {

    if (!empty($_POST['email']) && !empty($_POST['password'])) {

        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = mysqli_real_escape_string($con, $_POST['password']);

        $query = "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1";
        $query_run = mysqli_query($con, $query);

        if (mysqli_num_rows($query_run) > 0) {

            $user = mysqli_fetch_assoc($query_run);

            if($row['verify_status'] == "1")
            {
                $_SESSION['authenticated'] = TRUE;
                $_SESSION['auth_user'] = [
                    'username' => $row['name'],
                    'phone' => $row['phone'],
                    'email'=> $row['email'],
                ];
                $_SESSION['status'] = "You are Logged In Successfully.";
                header("Location: dashboard.php");
                exit(0);
            }
            else
            {
                $_SESSION['status'] = "Please verify your email address to login.";
                header("Location: login.php");
                exit(0);
            }

            // SET SESSION
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            // REDIRECT TO DASHBOARD
            header("Location: dashboard.php");
            exit();

        } else {
            $_SESSION['status'] = "Invalid Email or Password";
            header("Location: login.php");
            exit();
        }

    } else {
        $_SESSION['status'] = "All fields are mandatory";
        header("Location: login.php");
        exit();
    }

} else {
    header("Location: login.php");
    exit();
}