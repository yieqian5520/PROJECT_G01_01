<?php
require_once __DIR__ . '/../partials/session.php';

// Unset all session variables
$_SESSION = [];

// Delete session cookie (important)
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000,
    $params["path"], $params["domain"],
    $params["secure"], $params["httponly"]
  );
}

// Destroy session + rotate session id
session_destroy();
session_regenerate_id(true);

// Redirect to login/public page
header("Location: ../pages/login.php?msg=loggedout");
exit;
