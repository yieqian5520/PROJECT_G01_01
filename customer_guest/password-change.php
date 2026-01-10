<?php
session_start();

$page_title = "Password Change Update Form";
include_once __DIR__ . "/includes/header.php"; 
?>

<div class="py-5">
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
                <div class="card">
                    <div class="card-header">
                        <h4>Change Password</h4>
                    </div>

                    <div class="card-body">

                        <form action="password-reset-code.php" method="POST">

                            <input type="hidden" value="<?php if(isset($_GET['token'])){echo $_GET['token'];} ?>">

                            <div class="form-group mb-3">
                                <label>Email Address</label>
                                <input type="password" name="email" value="<?php if(isset($_GET['email'])){echo $_GET['email'];} ?>" class="form-control" placeholder="Enter Email Address" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Enter New Password" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
                            </div>

                            <div class="form-group mb-3">
                                <button type="submit" name="password_update" class="btn btn-success w-100">Update Password</button>
                            </div>

                        </form>

                    </div>
                </div>
</div>


<?php
include_once __DIR__ . "/includes/footer.php";
?>