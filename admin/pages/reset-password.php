<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . '/../config/config.php';

$sql = "SELECT * FROM users WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if($user === null){
  die("Token not found");
}

if(strtotime($user["reset_token_expires_at"]) <= time()){
  die("Token has expired");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/forgot.css">
</head>
<body>
  <div class="container">
    <div class="form-box">
        <h2>Reset Password</h2>
        <form method="post" action="../pages/process-reset-password.php">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
          
          <label for="password">New Password</label>
          <input type="password" id="password" name="password" required>

          <label for="password_confirmation">Repeat password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" required>
          <button>Send</button>
        </form>
    </div>
  </div>
</body>
</html>
  
  
