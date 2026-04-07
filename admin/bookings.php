<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$bookings = $conn->query("
    SELECT
        b.id,
        u.name,
        u.email,
        ps.slot_number,
        b.start_time,
        b.end_time,
        b.price,
        b.status
    FROM bookings b
    INNER JOIN users u ON u.id = b.user_id
    INNER JOIN parking_slots ps ON ps.id = b.slot_id
    ORDER BY b.created_at DESC
");

$pageTitle = 'All Bookings';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">All Bookings</h1>
        <p class="text-muted mb-0">Review every reservation made in the system.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Slot</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings->num_rows === 0): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No bookings found.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo (int) $booking['id']; ?></td>
                                <td><?php echo sanitize($booking['name']); ?></td>
                                <td><?php echo sanitize($booking['email']); ?></td>
                                <td><?php echo sanitize($booking['slot_number']); ?></td>
                                <td><?php echo sanitize(format_datetime($booking['start_time'])); ?></td>
                                <td><?php echo sanitize(format_datetime($booking['end_time'])); ?></td>
                                <td>Rs. <?php echo number_format((float) $booking['price'], 2); ?></td>
                                <td>
                                    <span class="badge text-bg-<?php echo booking_status_badge($booking['status']); ?>">
                                        <?php echo sanitize(ucfirst($booking['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
