<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

if (is_admin()) {
    redirect('admin/dashboard.php');
}

$bookingId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($bookingId <= 0) {
    set_flash('danger', 'Invalid booking selected.');
    redirect('user/history.php');
}

$cancelStmt = $conn->prepare("
    UPDATE bookings
    SET status = 'cancelled'
    WHERE id = ? AND user_id = ? AND status = 'active'
");
$cancelStmt->bind_param('ii', $bookingId, $_SESSION['user_id']);
$cancelStmt->execute();

if ($cancelStmt->affected_rows > 0) {
    set_flash('success', 'Booking cancelled successfully.');
} else {
    set_flash('warning', 'This booking could not be cancelled.');
}

$cancelStmt->close();
redirect('user/history.php');
