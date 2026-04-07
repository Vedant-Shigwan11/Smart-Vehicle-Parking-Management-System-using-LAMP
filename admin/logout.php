<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

logout_user();
session_start();
set_flash('success', 'Admin logged out successfully.');
redirect('admin/login.php');
