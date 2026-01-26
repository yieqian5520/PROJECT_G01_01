<?php

$token = $_GET["token"] ?? "";

if ($token === "") {
  die("Invalid reset link. Token is missing.");
}

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . '/../config/config.php';

// Ensure valid connection
if (!($mysqli instanceof mysqli)) {
  die("Database connection failed. Please try again later.");
}

$sql = "SELECT * FROM user WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
  die("Server error. Please try again later.");
}

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
  die("Invalid reset link. Token not found.");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
  die("This reset link has expired. Please request a new password reset.");
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
            <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 0.25rem;">
              Must be at least 8 characters with letters and numbers
            </small>
          </div>

          <div>
            <div class="label">Repeat Password</div>
            <div class="password-wrapper">
              <input class="input" type="password" id="password_confirmation" name="password_confirmation" required>
              <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">👁️</span>
            </div>
          </div>

          <div class="actions">
            <button class="btn btn-primary" type="submit">Reset Password</button>
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