<?php
require_once __DIR__ . '/../partials/auth_guard.php';
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user']['user_id'];

$uid = (int)$userId;
$stmt = $mysqli->prepare("SELECT full_name, email, phone FROM users WHERE user_id = ? LIMIT 1");
if ($stmt) {
  $stmt->bind_param('i', $uid);
  $stmt->execute();
  $res = $stmt->get_result();
  $me = $res ? $res->fetch_assoc() : null;
  $stmt->close();
} else {
  $me = null;
}

$err = $_GET['err'] ?? '';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
<div class="app-wrapper">
  <?php include __DIR__ . '/../partials/navbar.php'; ?>
  <?php include __DIR__ . '/../partials/sidebar_admin.php'; ?>

  <main class="app-main">
    <div class="app-content p-3">
      <div class="container-fluid">

        <?php if ($err === 'wrong_old'): ?>
          <div class="alert alert-danger">Old password is incorrect.</div>
        <?php elseif ($err === 'empty'): ?>
          <div class="alert alert-warning">Please fill required fields.</div>
        <?php endif; ?>

        <div class="row">
          <div class="col-lg-6">

            <div class="card mb-3">
              <div class="card-header"><h3 class="card-title">Edit Profile</h3></div>
              <div class="card-body">
                <form method="post" action="./profile_update.php">
                  <div class="mb-2">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" name="full_name" value="<?= htmlspecialchars($me['full_name'] ?? '') ?>" required>
                  </div>

                  <div class="mb-2">
                    <label class="form-label">Phone</label>
                    <input class="form-control" name="phone" value="<?= htmlspecialchars($me['phone'] ?? '') ?>">
                  </div>

                  <div class="mb-2">
                    <label class="form-label">Email (read-only)</label>
                    <input class="form-control" value="<?= htmlspecialchars($me['email'] ?? '') ?>" readonly>
                  </div>

                  <button class="btn btn-primary" type="submit">Save Profile</button>
                  <a class="btn btn-outline-secondary" href="./profile_view.php">Back</a>
                </form>
              </div>
            </div>

          </div>

          <div class="col-lg-6">

            <div class="card">
              <div class="card-header"><h3 class="card-title">Change Password</h3></div>
              <div class="card-body">
                <form method="post" action="./change_password.php">
                  <div class="mb-2">
                    <label class="form-label">Old Password</label>
                    <input type="password" class="form-control" name="old_password" required>
                  </div>

                  <div class="mb-2">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" name="new_password" required>
                  </div>    

                  <button class="btn btn-warning" type="submit">Update Password</button>
                </form>
                <div class="form-text mt-2">
                  For demo: set new password to something easy like Pucks123!
                </div>
              </div>
            </div>

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
