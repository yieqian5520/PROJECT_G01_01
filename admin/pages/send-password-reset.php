<?php

$email = $_POST['email'];

$token = bin2hex(random_bytes(16));

$token_hash = hash('sha256', $token);

$expiry = date("Y-m-d H:i:s", time() + 3600);

$mysqli = require __DIR__ . '/../config/config.php';

$sql = "UPDATE users
        SET reset_token_hash = ?, reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param('sss', $token_hash, $expiry, $email);

$stmt->execute();

if($conn->affected_rows){
    $mail = require __DIR__ . '/mailer.php';

    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

    Click <a href="http://example.com/reset-password.php?token=$token&email=$email">here</a> to reset your password. This link will expire in 1 hour.
    END;

    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
    }
}

echo "Message sent, please check your inbox.";