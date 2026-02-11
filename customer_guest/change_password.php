<?php
session_start();
include_once __DIR__ . "/dbcon.php";
include_once __DIR__ . "/mail_config.php"; // PHPMailer config

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

/* ===========================
   ⏱️ SESSION TIMEOUT (10 mins)
=========================== */
$timeout = 600; // seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

/* ===========================
   🔒 PROTECT PAGE
=========================== */
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    $_SESSION['status'] = "Please login to continue.";
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['auth_user']['id'];

/* ===========================
   🔐 HANDLE PASSWORD CHANGE
=========================== */
if (isset($_POST['change_password'])) {

    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$current_password || !$new_password || !$confirm_password) {
        $_SESSION['status'] = "All fields are required.";
        header("Location: change_password.php");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['status'] = "New password does not match.";
        header("Location: change_password.php");
        exit();
    }

    // Password rule
    $pattern = "/^(?=.*[A-Za-z])(?=.*\d).{8,}$/";
    if (!preg_match($pattern, $new_password)) {
        $_SESSION['status'] =
            "Password must be at least 8 characters and contain letters and numbers.";
        header("Location: change_password.php");
        exit();
    }

    // Fetch user
    $res = mysqli_query($con, "SELECT email, password, last_password FROM users WHERE id=$user_id LIMIT 1");
    if (!$res || mysqli_num_rows($res) !== 1) {
        $_SESSION['status'] = "User not found.";
        header("Location: change_password.php");
        exit();
    }

    $user = mysqli_fetch_assoc($res);

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['status'] = "Current password is incorrect.";
        header("Location: change_password.php");
        exit();
    }

    // 🔐 Block reuse of last password
    if (!empty($user['last_password']) && password_verify($new_password, $user['last_password'])) {
        $_SESSION['status'] = "You cannot reuse your previous password.";
        header("Location: change_password.php");
        exit();
    }

    // Update password
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $old_hash = $user['password'];

    $update = mysqli_query($con, "
        UPDATE users 
        SET password='$new_hash', last_password='$old_hash'
        WHERE id=$user_id LIMIT 1
    ");

    if ($update) {

        /* ===========================
           📧 EMAIL NOTIFICATION
        =========================== */
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USER, "Security Alert");
            $mail->addAddress($user['email']);

            $mail->isHTML(true);
            $mail->Subject = "Password Changed Successfully";
            $mail->Body = "
                <p>Your account password was changed.</p>
                <p>If this wasn't you, please contact support immediately.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // silent fail (security email shouldn't block change)
        }

        // Force re-login
        session_destroy();
        $_SESSION['status'] = "Password updated. Please login again.";
        header("Location: login.php");
        exit();
    }

    $_SESSION['status'] = "Password update failed.";
    header("Location: change_password.php");
    exit();
}

include_once __DIR__ . "/includes/header.php";
?>

<div class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <?php if (isset($_SESSION['status'])): ?>
          <div class="alert alert-info">
            <?= $_SESSION['status']; ?>
          </div>
          <?php unset($_SESSION['status']); ?>
        <?php endif; ?>

        <div class="card">
          <div class="card-header text-center">
            <h4>Change Password</h4>
          </div>

          <div class="card-body">
            <form method="POST">

              

              <div class="row mb-3 align-items-start">

    <!-- LEFT: Password Inputs -->
    <div class="col-md-8">

        <div class="mb-3">
            <label>Current Password</label>
            <input type="password" id="current" name="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>New Password</label>
            <input type="password" id="new" name="new_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" id="confirm" name="confirm_password" class="form-control" required>
        </div>

    </div>

    <!-- RIGHT: ONE Password Policy -->
    <div class="col-md-4">
        <div class="border rounded p-2 small bg-light">
            <strong>Password must:</strong>
            <ul class="mb-0 ps-3">
                <li>Be at least 8 characters</li>
                <li>Contain at least 1 letter (A–Z / a-z)</li>
                <li>Contain at least 1 number (0–9)</li>
            </ul>
        </div>
    </div>

</div>


              <!-- 👁️ Show / Hide -->
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" onclick="togglePassword()">
                <label class="form-check-label">Show passwords</label>
              </div>

              <div class="d-grid">
                <button type="submit" name="change_password" class="btn btn-warning text-dark">
                  Update Password
                </button>
              </div>

            </form>

            <hr>

            <div class="text-center">
              <a href="dashboard.php" class="btn btn-outline-secondary">← Back</a>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
function togglePassword() {
  ['current','new','confirm'].forEach(id => {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
  });
}
</script>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
