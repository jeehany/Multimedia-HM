<?php
session_start();

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'multimedia_hm';

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    die('Database connection error: ' . mysqli_connect_error());
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_user() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function has_role($role) {
    $u = current_user();
    if (!$u) return false;
    return $u['role'] === $role || $u['role'] === 'admin';
}

function is_admin() {
    $u = current_user();
    return $u && $u['role'] === 'admin';
}

function is_kepala() {
    $u = current_user();
    return $u && $u['role'] === 'kepala';
}

function is_staff() {
    $u = current_user();
    return $u && $u['role'] === 'staff';
}

function can_edit() {
    $u = current_user();
    if (!$u) return false;
    return in_array($u['role'], ['admin', 'staff']);
}

function can_approve() {
    $u = current_user();
    if (!$u) return false;
    return $u['role'] === 'kepala';
}

?>