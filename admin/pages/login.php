<?php
require_once __DIR__ . '/../partials/session.php';

if (isset($_SESSION['user'])) {
  header("Location: dashboard.php");
  exit;
}

$err = $_GET['err'] ?? '';
$msg = $_GET['msg'] ?? '';

$pageTitle = "Login | Pucks Coffee";
?>
<!doctype html>
<html lang="en">

<?php include __DIR__ . '/../partials/header.php'; ?>
<link rel="stylesheet" href="../css/auth.css">


<body class="login-page auth-page">
  <div class="login-box">
    <div class="card">
      <div class="card-body login-card-body">
        <?php if ($msg === 'loggedout'): ?>
          <div class="alert alert-success">You have been logged out successfully.</div>
        <?php elseif ($msg === 'reset_ok'): ?>
          <div class="alert alert-success">Password updated. Please login.</div>
        <?php endif; ?>

        <p class="login-box-msg">Sign in to start your session</p>

        <?php if ($err === 'login_required'): ?>
          <div class="alert alert-warning">Please login first.</div>
        <?php elseif ($err === 'invalid'): ?>
          <div class="alert alert-danger">Invalid email or password.</div>
        <?php elseif ($err === 'empty'): ?>
          <div class="alert alert-warning">Please fill in email and password.</div>
        <?php endif; ?>

        <form action="../auth/login_action.php" method="post" autocomplete="off">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
          </div>

          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <div class="input-group-text"><span class="bi bi-lock"></span></div>
          </div>

          <div class="row">
            <div class="col-8">
              <a href="forgot_password.php">Forgot password?</a>
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>

  <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
