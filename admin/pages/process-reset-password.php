<?php
// Include the database configuration
$mysqli = require __DIR__ . '/../config/config.php';

function renderResult($type, $title, $message, $primaryText = null, $primaryHref = null) {
  $badgeClass = "badge-warning";
  $badgeIcon  = "⚠️";
  if ($type === "success") { $badgeClass = "badge-success"; $badgeIcon = "✅"; }
  if ($type === "danger")  { $badgeClass = "badge-danger";  $badgeIcon = "❌"; }

  $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
  $safeMsg   = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

  $loginHref = "index1.php";

  echo '<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>' . $safeTitle . '</title>
  <link rel="stylesheet" href="../css/auth-ui.css">
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-head">
        <div>
          <h2 class="auth-title">' . $safeTitle . '</h2>
          <p class="auth-subtitle">' . $safeMsg . '</p>
        </div>
        <div class="badge ' . $badgeClass . '">' . $badgeIcon . '</div>
      </div>

      <div class="auth-body">
        <div class="actions">';

  if ($primaryText && $primaryHref) {
    echo '<a class="btn btn-primary" href="' . htmlspecialchars($primaryHref, ENT_QUOTES, 'UTF-8') . '">' .
         htmlspecialchars($primaryText, ENT_QUOTES, 'UTF-8') . '</a>';
  }

  
   echo '    </div>
      </div>
    </div>
  </div>
</body>
</html>';
  exit;
}

// Password validation function
function validatePassword($password) {
  // At least 8 characters
  if (strlen($password) < 8) {
    return "Password must be at least 8 characters long.";
  }
  
  // Must contain at least one letter
  if (!preg_match('/[a-zA-Z]/', $password)) {
    return "Password must contain at least one letter.";
  }
  
  // Must contain at least one number
  if (!preg_match('/[0-9]/', $password)) {
    return "Password must contain at least one number.";
  }
  
  return null; // Valid password
}

// Ensure valid connection
if (!($mysqli instanceof mysqli)) {
  renderResult("danger", "Database connection failed", "Unable to connect. Please try again later.");
}

$token = $_POST["token"] ?? "";
if ($token === "") {
  renderResult("danger", "Invalid request", "Missing reset token. Please request a new reset link.");
}

$token_hash = hash("sha256", $token);

// Check token
$sql = "SELECT * FROM user WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
  renderResult("danger", "Server error", "Error preparing SQL query.");
}

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
  renderResult("danger", "Invalid link", "This reset link is invalid. Please request a new password reset.", "Request New Reset", "forgot-password.php");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
  renderResult("danger", "Link expired", "This reset link has expired. Please request a new password reset.", "Request New Reset", "forgot-password.php");
}

// Check passwords
$password = $_POST["password"] ?? "";
$password_confirmation = $_POST["password_confirmation"] ?? "";

if ($password === "" || $password_confirmation === "") {
  renderResult("danger", "Missing information", "Please fill in all required fields and try again.");
}

if ($password !== $password_confirmation) {
  renderResult("danger", "Passwords do not match", "Please make sure both password fields are the same.");
}

// Validate password strength
$validationError = validatePassword($password);
if ($validationError !== null) {
  renderResult("danger", "Invalid password", $validationError, "Try Again", "reset-password.php?token=" . urlencode($token));
}

// Hash new password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Update password
$sql = "UPDATE user
        SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL
        WHERE id = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
  renderResult("danger", "Server error", "Error preparing SQL update.");
}

$stmt->bind_param("si", $password_hash, $user["id"]);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  renderResult("success", "Password reset successfully", "Your password has been updated. You can now log in.", "Return to Login", "index1.php");
} else {
  renderResult("danger", "Update failed", "Could not reset your password. Please try again.", "Request New Reset", "forgot-password.php");
}
?>