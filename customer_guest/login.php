<?php
include_once __DIR__ . "/includes/header.php"; 
?>

<section class="register-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="register-card">

                    <h5>Login Form</h5>

                    <form method="post" action="">

                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>


                        <button type="submit" class="register-btn w-100">
                            Login Now
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
