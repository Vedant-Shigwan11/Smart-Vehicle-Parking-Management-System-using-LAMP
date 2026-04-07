<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/Smart-Vehicle-Parking-System-using-LAMP/');
define('APP_NAME', 'Smart Vehicle Parking Booking System');
define('PRICE_PER_HOUR', 20);

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function format_datetime(string $value): string
{
    return date('d M Y, h:i A', strtotime($value));
}

function calculate_booking_price(string $startTime, string $endTime): float
{
    $seconds = strtotime($endTime) - strtotime($startTime);
    $hours = $seconds / 3600;

    return round($hours * PRICE_PER_HOUR, 2);
}

function booking_status_badge(string $status): string
{
    return match ($status) {
        'active' => 'success',
        'completed' => 'secondary',
        'cancelled' => 'danger',
        default => 'warning',
    };
}

function slot_status_badge(string $status): string
{
    return match ($status) {
        'available' => 'success',
        'maintenance' => 'warning',
        default => 'secondary',
    };
}

function is_post_request(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
