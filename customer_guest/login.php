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
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="example@email.com"
                                   required>
                        </div>

                        <div class="mb-3 password-wrapper">
                         <label>Password</label>
                           <div class="password-input-group">
                               <input type="password"
                                   name="password"
                                       class="form-control"
                                       placeholder="********"
                                       required>
                                      <span class="password-toggle">
                                        <br>
                                          <i class="bi bi-eye"></i>
                                       </span>
                             </div>
                        </div>


                        <button type="submit"
                                name="login_now_btn"
                                class="register-btn w-100">
                            Login Now
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {

        // ✅ Always get the input inside the same wrapper
        const input = toggle.previousElementSibling;
        const icon = toggle.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
});
</script>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
