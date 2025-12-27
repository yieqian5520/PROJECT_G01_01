<?php
require_once __DIR__ . '/../partials/auth_guard.php';
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user']['user_id'];

$uid = (int)$userId;
$stmt = $mysqli->prepare("SELECT full_name, email, phone, role FROM users WHERE user_id = ? LIMIT 1");
if ($stmt) {
  $stmt->bind_param('i', $uid);
  $stmt->execute();
  $res = $stmt->get_result();
  $me = $res ? $res->fetch_assoc() : null;
  $stmt->close();
} else {
  $me = null;
}

$msg = $_GET['msg'] ?? '';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
<div class="app-wrapper">
  <?php include __DIR__ . '/../partials/navbar.php'; ?>
  <?php include __DIR__ . '/../partials/sidebar_admin.php'; ?>

  <main class="app-main">
    <div class="app-content p-3">
      <div class="container-fluid">

        <?php if ($msg === 'updated'): ?>
          <div class="alert alert-success">Profile updated.</div>
        <?php elseif ($msg === 'pass_changed'): ?>
          <div class="alert alert-success">Password changed.</div>
        <?php endif; ?>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">My Profile</h3>
          </div>
          <div class="card-body">
            <p><b>Name:</b> <?= htmlspecialchars($me['full_name'] ?? '') ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($me['email'] ?? '') ?></p>
            <p><b>Phone:</b> <?= htmlspecialchars($me['phone'] ?? '-') ?></p>
            <p><b>Role:</b> <?= htmlspecialchars($me['role'] ?? '') ?></p>

            <a class="btn btn-primary" href="./profile_edit.php">Edit Profile</a>
            <a class="btn btn-outline-secondary" href="../auth/logout.php">Logout</a>
          </div>
        </div>

      </div>
    </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>

