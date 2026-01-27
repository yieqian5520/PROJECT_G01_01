<?php
session_start();
include_once __DIR__ . "/dbcon.php";
include_once __DIR__ . "/mail_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendemail_verify($name, $email, $verify_token)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);

        $verify_link = BASE_URL . "/verify-email.php?token=" . urlencode($verify_token);

        $mail->isHTML(true);
        $mail->Subject = "Email Verification - Pucks Coffee";
        $mail->Body = "
            <h2>Hi {$name}, welcome to Pucks Coffee!</h2>
            <p>Please verify your email to login:</p>
            <p><a href='{$verify_link}'>Click Here to Verify</a></p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if(isset($_POST['register_btn']))
{
    $name  = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);

    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Password match check
    if ($password !== $confirm) {
        $_SESSION['status'] = "Password and Confirm Password do not match.";
        header("Location: register.php");
        exit();
    }

    // Password requirements: 8+ chars, letters + numbers
    $pattern = "/^(?=.*[A-Za-z])(?=.*\d).{8,}$/";
    if (!preg_match($pattern, $password)) {
        $_SESSION['status'] = "Password must be at least 8 characters and include both letters and numbers.";
        header("Location: register.php");
        exit();
    }

    // Check existing email
    $check = mysqli_prepare($con, "SELECT id FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $_SESSION['status'] = "Email Already Exists.";
        header("Location: register.php");
        exit();
    }
    mysqli_stmt_close($check);

    $verify_token = md5(rand());
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($con, "INSERT INTO users (name, phone, email, password, verify_token, verify_status) VALUES (?, ?, ?, ?, ?, 0)");
    mysqli_stmt_bind_param($stmt, "sssss", $name, $phone, $email, $password_hash, $verify_token);

    if (mysqli_stmt_execute($stmt)) {
        $sent = sendemail_verify($name, $email, $verify_token);

        $_SESSION['status'] = $sent
            ? "Registration Successful. Please Verify Your Email."
            : "Registered, but email failed to send. Please use Resend.";

        header("Location: register.php");
        exit();
    } else {
        $_SESSION['status'] = "Registration Failed: " . mysqli_error($con);
        header("Location: register.php");
        exit();
    }
}

include_once __DIR__ . "/includes/header.php";
?>

<section class="register-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <?php if(isset($_SESSION['status'])): ?>
                    <div class="alert alert-info"><?= $_SESSION['status']; ?></div>
                    <?php unset($_SESSION['status']); ?>
                <?php endif; ?>

                <div class="register-card">
                    <h5>Create Your Account</h5>

                    <form action="" method="POST">

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

                        <div class="mb-3 password-wrapper">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control password-input" placeholder="********" required>
                            <span class="password-toggle">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>

                        <div class="mb-4 password-wrapper">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control password-input" placeholder="********" required>
                            <span class="password-toggle"><i class="bi bi-eye"></i></span>
                        </div>

                        <button type="submit" name="register_btn" class="register-btn w-100">Register Now</button>

                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- JS for toggle -->
<script>
document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
        const input = toggle.previousElementSibling;
        const icon = toggle.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});
</script>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
