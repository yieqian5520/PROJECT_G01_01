<?php

// Include the database configuration
$mysqli = require __DIR__ . '/../config/config.php';

// Ensure that the connection is a valid mysqli object
if (!($mysqli instanceof mysqli)) {
    die("Database connection failed");
}

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

// Prepare the SQL query to check if the token exists in the database
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    die("Error preparing SQL query: " . $mysqli->error);
}

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

// Check if passwords match
if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// Hash the new password
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Prepare the SQL query to update the user's password
$sql = "UPDATE users SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    die("Error preparing SQL query: " . $mysqli->error);
}

$stmt->bind_param("si", $password_hash, $user["id"]);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Password has been reset successfully.";
} else {
    die("Error resetting password. Please try again.");
}

?>