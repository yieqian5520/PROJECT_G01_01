<?php
session_start();

$mysqli = require __DIR__ . '/../config/config.php';

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    $_SESSION['forgot_error'] = "Please enter your email.";
    header("Location: forgot_password.php"); // <-- change to your actual forgot page
    exit;
}

/**
 * 1) Check if email exists
 */
$checkSql = "SELECT id FROM user WHERE email = ? LIMIT 1";
$checkStmt = $mysqli->prepare($checkSql);
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    // Email not found -> show message on forgot page
    $_SESSION['forgot_error'] = "We couldn’t find an account with that email address.";
    header("Location: forgot_password.php"); // <-- change to your actual forgot page
    exit;
}

/**
 * 2) If exists, generate token and update
 */
$token = bin2hex(random_bytes(16));
$token_hash = hash('sha256', $token);
$expiry = date("Y-m-d H:i:s", time() + 3600);

$updateSql = "UPDATE user
             SET reset_token_hash = ?, reset_token_expires_at = ?
             WHERE email = ?";

$updateStmt = $mysqli->prepare($updateSql);
$updateStmt->bind_param('sss', $token_hash, $expiry, $email);
$updateStmt->execute();

if ($mysqli->affected_rows) {
    $mail = require __DIR__ . '/mailer.php';

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'];

    $scriptDir   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $projectBase = preg_replace('#/admin/pages$#', '', $scriptDir);

    $baseUrl   = $scheme . '://' . $host . $projectBase;
    $resetLink = $baseUrl . "/admin/pages/reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($email);

    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->isHTML(true);
    $mail->Body = "Click <a href=\"$resetLink\">here</a> to reset your password. This link will expire in 1 hour.";

    try {
        $mail->send();
    } catch (Exception $e) {
        // optional: store mail error and redirect
        $_SESSION['forgot_error'] = "We couldn’t find an account with that email address.";
        header("Location: forgot_password.php");
        exit;
    }
}

// If success, show your success HTML page (what you already have)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Check your inbox</title>
  <link rel="stylesheet" href="../css/auth-ui.css">
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-head">
        <div>
          <h2 class="auth-title">Check your inbox</h2>
          <p class="auth-subtitle">We’ve sent a password reset link.</p>
        </div>
        <div class="badge badge-success">✅ Sent</div>
      </div>

      <div class="auth-body">
        <div class="note" style="margin-top:0;">
          Message sent, please check your inbox.
          <br><br>
          If you don’t see it, check your Spam/Junk folder or try again in a few minutes.
        </div>
      </div>
    </div>
  </div>
</body>
</html>
