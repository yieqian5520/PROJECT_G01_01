<?php
session_start();
include_once __DIR__ . "/dbcon.php";

// Protect dashboard
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    $_SESSION['status'] = "Please Login to Access User Dashboard.";
    header("Location: login.php");
    exit();
}

// Handle profile update
if (isset($_POST['update_profile'])) {

    $user_id = (int)($_SESSION['auth_user']['id'] ?? 0);
    if ($user_id <= 0) {
        $_SESSION['status'] = "Session error: user id missing. Please login again.";
        header("Location: login.php");
        exit();
    }

    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email    = mysqli_real_escape_string($con, $_POST['email']);
    $address  = mysqli_real_escape_string($con, $_POST['address']);
    $phone    = mysqli_real_escape_string($con, $_POST['phone']);

    // Password update (optional)
    $new_password_hash = null;
    if (!empty($_POST['password']) || !empty($_POST['confirm_password'])) {

    // Password requirement: min 8 chars, letters + numbers
    $pattern = "/^(?=.*[A-Za-z])(?=.*\d).{8,}$/";

    if ($_POST['password'] !== $_POST['confirm_password']) {
        $_SESSION['status'] = "Password and Confirm Password do not match!";
        header("Location: dashboard.php");
        exit();
    }

    if (!preg_match($pattern, $_POST['password'])) {
        $_SESSION['status'] =
            "Password must be at least 8 characters and contain letters and numbers.";
        header("Location: dashboard.php");
        exit();
    }

    $new_password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
}

    // Image upload (optional)
    $profile_path = null;

    if (!empty($_FILES['profile_image']['name'])) {

        // ✅ filesystem path: PROJECT_G01_01/uploads/profile/
        $folder = dirname(__DIR__) . "/uploads/profile/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            $_SESSION['status'] = "Only JPG, JPEG, PNG, WEBP allowed.";
            header("Location: dashboard.php");
            exit();
        }

        $file_name = time() . "_" . $user_id . "." . $ext;
        $target = $folder . $file_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {

            // ✅ URL base: /PROJECT_G01_01 (auto-detect)
            $baseUrl = dirname(dirname($_SERVER['SCRIPT_NAME'])); // /PROJECT_G01_01

            // ✅ save correct browser path
            $profile_path = $baseUrl . "/uploads/profile/" . $file_name;

        } else {
            $_SESSION['status'] = "Upload failed. Check folder permissions.";
            header("Location: dashboard.php");
            exit();
        }
    }

    // Build UPDATE query
    $sql = "UPDATE users SET name='$username', email='$email', address='$address', phone='$phone'";
    if ($new_password_hash) $sql .= ", password='$new_password_hash'";
    if ($profile_path) $sql .= ", profile_image='$profile_path'";
    $sql .= " WHERE id=$user_id LIMIT 1";

    if (mysqli_query($con, $sql)) {
        // Update session so it shows immediately
        $_SESSION['auth_user']['username'] = $username;
        $_SESSION['auth_user']['email']    = $email;
        $_SESSION['auth_user']['address']  = $address;
        $_SESSION['auth_user']['phone']    = $phone;
        if ($profile_path) $_SESSION['auth_user']['profile_image'] = $profile_path;

        $_SESSION['status'] = "Profile Updated Successfully";
    } else {
        $_SESSION['status'] = "Update Failed: " . mysqli_error($con);
    }

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

                        <div class="text-center mb-3">
                            <img
                              src="<?= !empty($_SESSION['auth_user']['profile_image']) ? $_SESSION['auth_user']['profile_image'] : 'https://via.placeholder.com/120' ?>"
                              class="rounded-circle"
                              width="120"
                              height="120"
                              style="object-fit:cover;"
                            >
                        </div>

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

                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="btn btn-warning text-dark">
                                    Update Profile
                                </button>
                            </div>

                        </form>

                        <hr>

                        <div class="d-flex justify-content-between mt-3">

                        <!-- Change Password -->
                        <a href="change_password.php" class="btn btn-outline-secondary">
                            Change Password
                        </a>

                        <!-- Logout -->
                        <form action="logout.php" method="POST">
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
</div>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
