<?php
session_start();
include_once __DIR__ . "/includes/header.php"; 
?>

<section class="register-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert">

                    <?php
                       if(isset($_SESSION['status']))
                       {
                        echo "<h5>".$_SESSION['status']."</h5>";
                        unset($_SESSION['status']);
                       }
                       ?>

                </div>
                <div class="register-card">

                    <h5>Create Your Account</h5>

                    <form action="code.php" method="POST">

                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                        </div>

                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="01XXXXXXXX" required>
                        </div>

                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>

                        <div class="mb-4">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="********" required>
                        </div>

                        <button type="submit" name="register_btn" class="register-btn w-100">
                            Register Now
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
