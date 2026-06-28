<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function auth_require(string $role = '') : void
{
    
    if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
        session_destroy();
    
    header('Location: /UNISTAY/wk7/login.php?error=unauthorised_access');

$redirect = ($_SESSION['role'] === 'admin')
            ? '/UNISTAY/wk7/admin/dashboard.php'
            : '/UNISTAY/wk7/student/dashboard.php';
header('Location: ' . $redirect . '?error=forbidden');
        exit;
    }
}

function current_user() : array
{
    return [
        'id'         => $_SESSION['user_id']    ?? null,
        'name'       => $_SESSION['full_name']  ?? 'Guest',
        'role'       => $_SESSION['role']       ?? '',
        'student_id' => $_SESSION['student_id'] ?? '',
    ];
}