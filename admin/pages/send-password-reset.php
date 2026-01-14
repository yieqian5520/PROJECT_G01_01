<?php

$email = $_POST['email'];

$token = bin2hex(random_bytes(16));

$token_hash = hash('sha256', $token);

$expiry = date("Y-m-d H:i:s", time() + 3600);

$mysqli = require __DIR__ . '/../config/config.php';

$sql = "UPDATE users
        SET reset_token_hash = ?, reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param('sss', $token_hash, $expiry, $email);

$stmt->execute();

if ($mysqli->affected_rows) {
    $mail = require __DIR__ . '/mailer.php';

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'];

    $scriptDir   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $projectBase = preg_replace('#/admin/pages$#', '', $scriptDir);

    $baseUrl   = $scheme . '://' . $host . $projectBase;
    $resetLink = $baseUrl . "/admin/pages/reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($email);

    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->isHTML(true);
    $mail->Body = "Click <a href=\"$resetLink\">here</a> to reset your password. This link will expire in 1 hour.";

    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
    }
}

echo "Message sent, please check your inbox.";