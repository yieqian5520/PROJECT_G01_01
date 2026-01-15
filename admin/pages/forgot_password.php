<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>

  <!-- Use the universal auth UI -->
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
        <form class="form" method="post" action="../pages/send-password-reset.php">
          <div>
            <div class="label">Email address</div>
            <input class="input" type="email" id="email" placeholder="Enter your email address" name="email" required>
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
