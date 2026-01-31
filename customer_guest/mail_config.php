<?php
// ========== SMTP CONFIG ==========
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_USER", "bananacoffee06@gmail.com");
define("SMTP_PASS", "rvnz yboi llgt bwiz"); // <- change
define("SMTP_PORT", 587);
define("SMTP_FROM_EMAIL", "bananacoffee06@gmail.com");
define("SMTP_FROM_NAME", "Pucks Coffee");

// ========== BASE URL (change to your project path) ==========
define("BASE_URL",
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
    . "://"
    . $_SERVER['HTTP_HOST']
    . dirname($_SERVER['SCRIPT_NAME'])
);
?>
