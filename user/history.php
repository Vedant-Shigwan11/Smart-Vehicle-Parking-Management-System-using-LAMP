<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

if (is_admin()) {
    redirect('admin/dashboard.php');
}

$historyStmt = $conn->prepare("
    SELECT
        b.id,
        b.start_time,
        b.end_time,
        b.price,
        b.status,
        ps.slot_number
    FROM bookings b
    INNER JOIN parking_slots ps ON ps.id = b.slot_id
    WHERE b.user_id = ?
    ORDER BY b.start_time DESC
");
$historyStmt->bind_param('i', $_SESSION['user_id']);
$historyStmt->execute();
$bookings = $historyStmt->get_result();

$pageTitle = 'Booking History';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">My Booking History</h1>
        <p class="text-muted mb-0">Track your slot reservations and booking status.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>user/dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Slot</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings->num_rows === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No bookings found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo (int) $booking['id']; ?></td>
                                <td><?php echo sanitize($booking['slot_number']); ?></td>
                                <td><?php echo sanitize(format_datetime($booking['start_time'])); ?></td>
                                <td><?php echo sanitize(format_datetime($booking['end_time'])); ?></td>
                                <td>Rs. <?php echo number_format((float) $booking['price'], 2); ?></td>
                                <td>
                                    <span class="badge text-bg-<?php echo booking_status_badge($booking['status']); ?>">
                                        <?php echo sanitize(ucfirst($booking['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] === 'active'): ?>
                                        <a href="<?php echo BASE_URL; ?>user/cancel.php?id=<?php echo (int) $booking['id']; ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to cancel this booking?');">
                                            Cancel
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">No action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$historyStmt->close();
require_once __DIR__ . '/../includes/footer.php';
?>
