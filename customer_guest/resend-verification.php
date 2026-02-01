<?php
session_start();
include_once __DIR__ . "/includes/header.php";
?>

<section class="container" style="padding:60px 16px;">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <?php if (isset($_SESSION['status'])): ?>
                <div class="alert alert-info text-center">
                    <?= $_SESSION['status']; unset($_SESSION['status']); ?>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-body">
                    <h5 class="text-center mb-3">Resend Verification Email</h5>

                    <form action="resend-code.php" method="POST">
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <button type="submit"
                                name="resend_email_verify_btn"
                                class="btn btn-primary w-100">
                            Resend Verification
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
