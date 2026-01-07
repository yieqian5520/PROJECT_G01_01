<?php
// Protect page & start session
include_once __DIR__ . "/authentication.php";

$page_title = "Dashboard";
include_once __DIR__ . "/includes/header.php";
?>

<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4>User Dashboard</h4>
                    </div>

                    <div class="card-body">
                        <h4 class="mb-3">Access when you are Logged IN</h4>
                        <hr>

                        <h5>
                            Username:
                            <span class="text-primary">
                                <?= htmlspecialchars($_SESSION['auth_user']['username']); ?>
                            </span>
                        </h5>

                        <h5>
                            Email:
                            <span class="text-primary">
                                <?= htmlspecialchars($_SESSION['auth_user']['email']); ?>
                            </span>
                        </h5>

                        <h5>
                            Phone No:
                            <span class="text-primary">
                                <?= htmlspecialchars($_SESSION['auth_user']['phone']); ?>
                            </span>
                        </h5>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
