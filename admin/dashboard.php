<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$stats = [
    'total_bookings' => 0,
    'available_slots' => 0,
    'active_bookings' => 0,
    'total_users' => 0,
];

$stats['total_bookings'] = (int) ($conn->query('SELECT COUNT(*) AS total FROM bookings')->fetch_assoc()['total'] ?? 0);
$stats['available_slots'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM parking_slots WHERE status = 'available'")->fetch_assoc()['total'] ?? 0);
$stats['active_bookings'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'active'")->fetch_assoc()['total'] ?? 0);
$stats['total_users'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'user'")->fetch_assoc()['total'] ?? 0);

$recentBookings = $conn->query("
    SELECT b.id, u.name, ps.slot_number, b.start_time, b.end_time, b.status
    FROM bookings b
    INNER JOIN users u ON u.id = b.user_id
    INNER JOIN parking_slots ps ON ps.id = b.slot_id
    ORDER BY b.created_at DESC
    LIMIT 5
");

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Admin Dashboard</h1>
        <p class="text-muted mb-0">Monitor bookings, slots, and registered users.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo BASE_URL; ?>admin/manage_slots.php" class="btn btn-outline-primary">Manage Slots</a>
        <a href="<?php echo BASE_URL; ?>admin/bookings.php" class="btn btn-primary">View All Bookings</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Total Bookings</p>
                <h2 class="h3 mb-0"><?php echo $stats['total_bookings']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Available Slots</p>
                <h2 class="h3 mb-0"><?php echo $stats['available_slots']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Active Bookings</p>
                <h2 class="h3 mb-0"><?php echo $stats['active_bookings']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Registered Users</p>
                <h2 class="h3 mb-0"><?php echo $stats['total_users']; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h2 class="h4 mb-3">Recent Bookings</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Slot</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentBookings->num_rows === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No bookings available.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($booking = $recentBookings->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo (int) $booking['id']; ?></td>
                                <td><?php echo sanitize($booking['name']); ?></td>
                                <td><?php echo sanitize($booking['slot_number']); ?></td>
                                <td><?php echo sanitize(format_datetime($booking['start_time'])); ?></td>
                                <td><?php echo sanitize(format_datetime($booking['end_time'])); ?></td>
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
