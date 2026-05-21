<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}
?>
