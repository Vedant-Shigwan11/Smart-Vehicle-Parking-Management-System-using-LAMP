<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

function is_logged_in(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['user_role']);
}

function is_admin(): bool
{
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('danger', 'Please log in to continue.');
        redirect('user/login.php');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        set_flash('danger', 'Admin access is required.');
        redirect('admin/login.php');
    }
}

function login_user(array $user): void
{
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}
