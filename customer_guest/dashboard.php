<?php
// 🔑 Start session FIRST (nothing before this line)
session_start();

// 🔒 Protect dashboard (must be logged in)
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    $_SESSION['status'] = "Please Login to Access User Dashboard.";
    header("Location: login.php");
    exit();
}

$page_title = "Dashboard";
include_once __DIR__ . "/includes/header.php";
?>

<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <?php if (isset($_SESSION['status'])): ?>
                    <div class="alert alert-success">
                        <h5><?= $_SESSION['status']; ?></h5>
                    </div>
                    <?php unset($_SESSION['status']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h4>User Profile</h4>
                    </div>

                    <div class="card-body">
                        <h4>Access when you are Logged IN</h4>
                        <hr>

                        <h5>Username: <?= $_SESSION['auth_user']['username'] ?? 'N/A'; ?></h5>
                        <h5>Email: <?= $_SESSION['auth_user']['email'] ?? 'N/A'; ?></h5>
                        <h5>Phone No: <?= $_SESSION['auth_user']['phone'] ?? 'N/A'; ?></h5>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<br>

<?php 
include_once __DIR__ . "/includes/footer.php"; 
?>
