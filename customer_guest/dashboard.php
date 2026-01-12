<?php
session_start();

// Protect dashboard
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    $_SESSION['status'] = "Please Login to Access User Dashboard.";
    header("Location: login.php");
    exit();
}

// Handle profile update
if (isset($_POST['update_profile'])) {

    // Basic fields
    $_SESSION['auth_user']['username'] = $_POST['username'];
    $_SESSION['auth_user']['email']    = $_POST['email'];
    $_SESSION['auth_user']['address']  = $_POST['address'];
    $_SESSION['auth_user']['phone']    = $_POST['phone'];

    // Password update (only if filled)
    if (!empty($_POST['password']) || !empty($_POST['confirm_password'])) {

        if ($_POST['password'] !== $_POST['confirm_password']) {
            $_SESSION['status'] = "Password and Confirm Password do not match!";
            header("Location: dashboard.php");
            exit();
        }

        // Hash password
        $_SESSION['auth_user']['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Profile image upload
    if (!empty($_FILES['profile_image']['name'])) {

        $folder = "uploads/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_name = time() . "_" . $_FILES['profile_image']['name'];
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $folder . $file_name);

        $_SESSION['auth_user']['profile_image'] = $folder . $file_name;
    }

    $_SESSION['status'] = "Profile Updated Successfully";
    header("Location: dashboard.php");
    exit();
}

include_once __DIR__ . "/includes/header.php";
?>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">

                <?php if (isset($_SESSION['status'])): ?>
                    <div class="alert alert-info">
                        <?= $_SESSION['status']; ?>
                    </div>
                    <?php unset($_SESSION['status']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header text-center">
                        <h4>User Profile</h4>
                    </div>

                    <div class="card-body">

                        <!-- Profile Image -->
                        <div class="text-center mb-3">
                            <img src="<?= $_SESSION['auth_user']['profile_image'] ?? 'https://via.placeholder.com/120' ?>"
                                 class="rounded-circle" width="120" height="120">
                        </div>

                        <!-- Profile Form -->
                        <form method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label>Change Profile Image</label>
                                <input type="file" name="profile_image" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="username" class="form-control"
                                       value="<?= $_SESSION['auth_user']['username'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= $_SESSION['auth_user']['email'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control"
                                       value="<?= $_SESSION['auth_user']['address'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control"
                                       value="<?= $_SESSION['auth_user']['phone'] ?? '' ?>">
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control">
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="update_profile"
                                        class="btn btn-warning text-dark">
                                    Update Profile
                                </button>
                            </div>

                        </form>

                        <hr>

                        <!-- Logout -->
                        <form action="logout.php" method="POST" class="text-center">
                            <button type="submit" class="btn btn-danger">
                                Logout
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
