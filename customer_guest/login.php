<?php
session_start();
if(isset( $_SESSION['authenticated'])){

    $_SESSION['status'] = "Please Login to Access User Dashboard.";
    header("Location: dashboard.php");
    exit(0);
}
include_once __DIR__ . "/includes/header.php";
?>

<section class="register-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

            <?php
            if(isset($_SESSION['status']))
            {
                ?>
                <div class="alert alert-success">
                    <h5><?= $_SESSION['status'];?></h5>
                </div>
                <?php
                unset($_SESSION['status']);
            }
            ?>
                <div class="register-card">

                    <h5>Login Form</h5>

                    <!-- SHOW ERROR MESSAGE -->
                    <?php if (isset($_SESSION['status'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['status']; ?>
                        </div>
                        <?php unset($_SESSION['status']); ?>
                    <?php endif; ?>

                    <form action="logincode.php" method="POST">

                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="example@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="********" required>
                        </div>

                        <button type="submit" name="login_now_btn" class="register-btn w-100">
                            Login Now
                        </button>

                    </form>

                    <hr>
                    <h5>
                        Did not receive your verification email?
                        <a href="resend-email-verification.php">Resend</a>
                    </h5>

                </div>
            </div>
        </div>
    </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
