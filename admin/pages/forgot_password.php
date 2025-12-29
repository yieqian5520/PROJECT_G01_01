<?php
require_once __DIR__ . '/../partials/session.php';

if (isset($_SESSION['user'])) {
  header("Location: dashboard.php");
  exit;
}

$err = $_GET['err'] ?? '';
$msg = $_GET['msg'] ?? '';

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$demoLink = $_SESSION['demo_reset_link'] ?? null;
unset($_SESSION['demo_reset_link']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forgot Password | Pucks Coffee</title>
  <link rel="stylesheet" href="../css/adminlte.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/auth.css">
</head>


<body class="login-page auth-page">
  <div class="login-box">
    <div class="card">
      <div class="card-body login-card-body">

        <p class="login-box-msg">Forgot your password?</p>

        <p class="text-muted mb-3">
          Enter your registered email. We will provide a reset link.
        </p>

        <?php if ($err === 'empty'): ?>
          <div class="alert alert-warning">Please enter your email.</div>
        <?php elseif ($err === 'invalid_email'): ?>
          <div class="alert alert-danger">Please enter a valid email address.</div>
        <?php endif; ?>

        <?php if ($flash): ?>
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($flash) ?>
          </div>
        <?php endif; ?>

        

        <?php if ($demoLink): ?>
          <div class="alert alert-secondary">
            <div class="fw-semibold mb-1">Demo reset link:</div>
            <a href="<?= htmlspecialchars($demoLink) ?>"><?= htmlspecialchars($demoLink) ?></a>
          </div>
        <?php endif; ?>

        <form action="../auth/forgot_action.php" method="post" autocomplete="off">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
          </div>

          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-send me-1"></i>Send reset link
          </button>
        </form>

        <p class="mt-3 mb-0 text-center">
          <a href="login.php" class="text-decoration-none">Back to login</a>
        </p>
      </div>
    </div>
  </div>

</body>
</html>
