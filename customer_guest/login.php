<?php
session_start();
if (isset($_SESSION['authenticated'])) {
    header("Location: dashboard.php");
    exit();
}
include_once __DIR__ . "/includes/header.php";
?>

<section class="register-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <?php if(isset($_SESSION['status'])): ?>
                    <div class="alert alert-info"><?= $_SESSION['status']; ?></div>
                    <?php unset($_SESSION['status']); ?>
                <?php endif; ?>

                <div class="register-card">
                    <h5>Login</h5>

                    <form action="logincode.php" method="POST">

                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                        </div>

                        <div class="mb-3 password-wrapper">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control password-input" placeholder="********" required>
                            <span class="password-toggle"><i class="bi bi-eye"></i></span>
                        </div>

                        <button type="submit" name="login_now_btn" class="register-btn w-100">Login Now</button>

                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

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
