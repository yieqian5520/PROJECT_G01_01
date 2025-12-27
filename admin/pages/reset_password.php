<?php
require_once __DIR__ . '/../partials/session.php';

$token = $_GET['token'] ?? '';
if ($token === '') {
  header("Location: login.php");
  exit;
}

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>

  <!-- FIX: correct CSS path -->
  <link rel="stylesheet" href="../css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <style>
    .auth-bg{
      min-height: 100vh;
      background:
        linear-gradient(135deg, rgba(17,24,39,.88), rgba(2,132,199,.55)),
        url("../assets/img/boxed-bg.jpg");
      background-size: cover;
      background-position: center;
    }
  </style>
</head>

<body class="hold-transition auth-bg">
  <div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="reset-card">
      <div class="login-box">

    <div class="card card-outline card-success shadow-lg">
      <div class="card-header text-center py-4">
        <a href="login.php" class="h1 text-decoration-none">
          <img src="../assets/img/p.png" alt="Logo" style="height:34px" class="me-2">
          Pucks Coffee
        </a>
        <div class="text-muted mt-2">Set a new password</div>
      </div>

      <div class="card-body login-card-body">
        <p class="login-box-msg">
          Create a strong password and confirm it.
        </p>

        <?php if ($flash_success): ?>
          <div class="alert alert-success">
            <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($flash_success) ?>
          </div>
        <?php endif; ?>

        <?php if ($flash_error): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($flash_error) ?>
          </div>
        <?php endif; ?>

        <form action="../auth/reset_action.php" method="post" id="resetForm" autocomplete="off">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <div class="input-group mb-3">
            <input
              type="password"
              name="password"
              id="password"
              class="form-control"
              placeholder="New password"
              minlength="6"
              required
            >
            <button class="input-group-text" type="button" id="togglePass" aria-label="Show password">
              <span class="bi bi-eye"></span>
            </button>
          </div>

          <div class="input-group mb-3">
            <input
              type="password"
              name="confirm_password"
              id="confirm_password"
              class="form-control"
              placeholder="Confirm new password"
              minlength="6"
              required
            >
            <button class="input-group-text" type="button" id="toggleConfirm" aria-label="Show confirm password">
              <span class="bi bi-eye"></span>
            </button>
          </div>

          <div class="text-muted small mb-3">
            Tip: use at least 8 characters with a mix of letters and numbers.
          </div>

          <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-shield-lock me-1"></i>Update password
          </button>
        </form>

        <p class="mt-3 mb-0 text-center">
          <a href="login.php" class="text-decoration-none">
            Back to login
          </a>
        </p>
      </div>
    </div>

  </div>
    </div>
  </div>
  <script>
    const pass = document.getElementById('password');
    const confirmPass = document.getElementById('confirm_password');

    function toggle(input, btnId) {
      const btn = document.getElementById(btnId);
      btn.addEventListener('click', () => {
        input.type = (input.type === 'password') ? 'text' : 'password';
        btn.querySelector('span').className = (input.type === 'password') ? 'bi bi-eye' : 'bi bi-eye-slash';
      });
    }
    toggle(pass, 'togglePass');
    toggle(confirmPass, 'toggleConfirm');

    document.getElementById('resetForm').addEventListener('submit', (e) => {
      if (pass.value !== confirmPass.value) {
        e.preventDefault();
        alert('Passwords do not match.');
      }
    });
  </script>
</body>
</html>


