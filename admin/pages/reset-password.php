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
  <title>Reset Password</title>

  <!-- Use the universal CSS -->
  <link rel="stylesheet" href="../css/auth-ui.css">
</head>

<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-head">
        <div>
          <h2 class="auth-title">Reset Password</h2>
          <p class="auth-subtitle">Please enter your new password.</p>
        </div>
        <div class="badge badge-warning">🔒 Reset</div>
      </div>

      <div class="auth-body">
        <form class="form" method="post" action="../pages/process-reset-password.php">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <div>
            <div class="label">New Password</div>
            <div class="password-wrapper">
              <input class="input" type="password" id="password" name="password" required>
              <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
            </div>
          </div>

          <div>
            <div class="label">Repeat Password</div>
            <div class="password-wrapper">
              <input class="input" type="password" id="password_confirmation" name="password_confirmation" required>
              <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">👁️</span>
            </div>
          </div>


          <div class="actions">
            <button class="btn btn-primary" type="submit">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    
function togglePassword(inputId, el) {
  const input = document.getElementById(inputId);

  if (input.type === "password") {
    input.type = "text";
    el.textContent = "🙈";
  } else {
    input.type = "password";
    el.textContent = "👁️";
  }
}
</script>

</body>
</html>

  
  
