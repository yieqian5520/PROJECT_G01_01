<?php
session_start();
include_once __DIR__ . "/dbcon.php";

if (!isset($_GET['token'])) {
    die("Invalid request");
}

$token = $_GET['token'];

// Check token
$stmt = $con->prepare("SELECT id FROM users WHERE verify_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($user_id);

if ($stmt->num_rows === 0) {
    die("Invalid or expired token.");
}
$stmt->fetch();
$stmt->close();

if(isset($_POST['reset_password_btn'])) {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['status'] = "Passwords do not match.";
    } elseif(strlen($password) < 8) {
        $_SESSION['status'] = "Password must be at least 8 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $con->prepare("UPDATE users SET password=?, verify_token='' WHERE id=?");
        $update->bind_param("si", $hash, $user_id);
        $update->execute();
        $update->close();

        $_SESSION['status'] = "Password updated! You can login now.";
        header("Location: login.php");
        exit();
    }
}

include_once __DIR__ . "/includes/header.php";
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if(isset($_SESSION['status'])): ?>
                <div class="alert alert-info"><?= $_SESSION['status']; unset($_SESSION['status']); ?></div>
            <?php endif; ?>

            <div class="card p-4">
                <h5>Reset Password</h5>
                <form method="POST">
                    <!-- New Password -->
                    <div class="mb-3 password-wrapper">
                        <label>New Password</label>
                        <div class="password-input-group">
                            <input type="password" name="password" class="form-control password-input" placeholder="********" required>
                            <span class="password-toggle">
                                <br>
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3 password-wrapper">
                        <label>Confirm Password</label>
                        <div class="password-input-group">
                            <input type="password" name="confirm_password" class="form-control password-input" placeholder="********" required>
                            <span class="password-toggle">
                                <br>
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" name="reset_password_btn" class="btn btn-warning w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS for Eye Toggle -->
<script>
document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
        const input = toggle.previousElementSibling;
        const icon = toggle.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});
</script>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
