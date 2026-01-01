<?php

session_start();

$mysqli = require __DIR__ . '/../config/config.php';


if(isset($_POST['register'])){
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  $checkEmail = $mysqli->query("SELECT email FROM users WHERE email='$email'");
  if($checkEmail->num_rows > 0){
    $_SESSION['register_error'] = "Email already registered.";
    $_SESSION['active_form'] = "register";
  } else {
    $mysqli->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");
  }

  header("Location: index1.php");
  exit();
}


if(isset($_POST['login'])){
  $email = $_POST['email'];
  $password = $_POST['password'];

  $result = $mysqli->query("SELECT * FROM users WHERE email='$email'");
  if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    if(password_verify($password, $user['password'])){
      $_SESSION['id'] = $user['id']; 
      $_SESSION['name'] = $user['name'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['role'] = $user['role'];

      if($user['role'] === 'admin'){
        header("Location: ../pages/dashboard.php");
      } else {
        header("Location: ../pages/staff_dashboard.php");
      }     
      exit;
    } 
  }

  $_SESSION['login_error'] = "Invalid email or password.";
  $_SESSION['active_form'] = "login";
  header("Location: index1.php");
  exit;
}

?>