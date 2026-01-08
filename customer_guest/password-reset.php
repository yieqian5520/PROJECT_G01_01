<?php
session_start();

$page_title = "Password Reset Form";
include_once __DIR__ . "/includes/header.php"; 
?>

<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                    <?php
                       if(isset($_SESSION['status']))
                       {
                        echo "<h5>".$_SESSION['status']."</h5>";
                        unset($_SESSION['status']);
                       }
                    ?>

                <div class="card">
                    <div class="card-header">
                        <h4>Reset Password</h4>
                    </div>

                    <div class="card-body">

                        <form action="" method="POST">

                            <div class="mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" required class="form-control" />
                            </div>

                            <div class="mb-3">
                                <button type="submit" name="password_reset_link" class="btn btn-primary">Send Password Reset Link</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . "/includes/footer.php";
?>