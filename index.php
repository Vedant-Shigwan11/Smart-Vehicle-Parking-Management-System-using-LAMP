<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    if (is_admin()) {
        header('Location: admin/dashboard.php');
        exit;
    }

    header('Location: user/dashboard.php');
    exit;
}

header('Location: user/login.php');
exit;
