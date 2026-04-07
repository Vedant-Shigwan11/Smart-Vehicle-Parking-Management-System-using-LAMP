<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? APP_NAME;
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/style.css">
</head>
<body class="app-body">
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">
                <?php echo APP_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <?php if (is_logged_in() && !is_admin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>user/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>user/history.php">My Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>user/logout.php">Logout</a></li>
                    <?php elseif (is_admin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard.php">Admin Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/manage_slots.php">Manage Slots</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/bookings.php">Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/manage_users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>user/login.php">User Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>user/register.php">Register</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/login.php">Admin Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo sanitize($flash['type']); ?> alert-dismissible fade show" role="alert">
                <?php echo sanitize($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
