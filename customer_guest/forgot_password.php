<?php
session_start();
include_once __DIR__ . "/dbcon.php";
include_once __DIR__ . "/mail_config.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_reset_email($name, $email, $token) {
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

        $reset_link = BASE_URL . "/reset_password.php?token=" . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = "Reset Your Password - Pucks Coffee";
        $mail->Body = "
            <h2>Hello {$name}</h2>
            <p>Click below to reset your password:</p>
            <p><a href='{$reset_link}'>Reset Password</a></p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if(isset($_POST['forgot_password_btn'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    // Check if email exists
    $stmt = $con->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $name);

    if($stmt->num_rows > 0) {
        $stmt->fetch();

        // Generate unique token
        $token = md5(rand());
        $update = $con->prepare("UPDATE users SET verify_token=? WHERE id=?");
        $update->bind_param("si", $token, $user_id);
        $update->execute();
        $update->close();

        $sent = send_reset_email($name, $email, $token);

        $_SESSION['status'] = $sent
            ? "Reset link sent to your email!"
            : "Failed to send reset email.";
    } else {
        $_SESSION['status'] = "Email not found!";
    }
    $stmt->close();
    header("Location: forgot_password.php");
    exit();
}

include_once __DIR__ . "/includes/header.php";
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if(isset($_SESSION['status'])): ?>
                <div class="alert alert-info"><?= $_SESSION['status']; unset($_SESSION['status']); ?></div>
            <?php endif; ?>

            <div class="card p-4">
                <h5>Forgot Password</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" name="forgot_password_btn" class="btn btn-warning w-100">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
