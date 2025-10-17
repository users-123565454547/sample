<?php
// auth.php - Authentication functions
session_start();

// Don't redefine getDBConnection here, just include the database config
require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    if (!isLoggedIn()) return false;
    return ($_SESSION['role'] ?? '') === $role;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    $currentRole = $_SESSION['role'] ?? '';
    if ($currentRole !== $role && $currentRole !== 'admin') {
        header("Location: dashboard.php");
        exit();
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null
        ];
    }
    return null;
}
?>