<?php
session_start();
require_once 'db.php';

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function login($username, $password) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Protect admin pages
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}
?> 