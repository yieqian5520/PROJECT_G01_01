<?php
session_start();

$err = $_SESSION['forgot_error'] ?? '';
unset($_SESSION['forgot_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>

  <link rel="stylesheet" href="../css/auth-ui.css">
</head>

<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-head">
        <div>
          <h2 class="auth-title">Forgot Password</h2>
          <p class="auth-subtitle">Enter your email to receive a reset link.</p>
        </div>
        <div class="badge badge-warning">📧 Reset</div>
      </div>

      <div class="auth-body">

        <?php if (!empty($err)): ?>
          <div class="note note-warning" style="margin-top:0;">
            <?= htmlspecialchars($err) ?>
            <div style="margin-top:6px;font-size:12.5px;opacity:.85;">
              Please check for typos or try another email.
            </div>
          </div>
        <?php endif; ?>

        <form class="form" method="post" action="send-password-reset.php">
          <div>
            <div class="label">Email address</div>
            <input class="input" type="email" id="email"
                   placeholder="Enter your email address"
                   name="email" required>
          </div>

          <div class="actions">
            <button class="btn btn-primary" type="submit" name="send">Send</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</body>
</html>
