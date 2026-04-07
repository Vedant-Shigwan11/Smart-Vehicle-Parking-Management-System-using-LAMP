<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

if (is_admin()) {
    redirect('admin/dashboard.php');
}

$slotsQuery = "
    SELECT
        ps.id,
        ps.slot_number,
        ps.status,
        EXISTS (
            SELECT 1
            FROM bookings b
            WHERE b.slot_id = ps.id
              AND b.status = 'active'
              AND NOW() BETWEEN b.start_time AND b.end_time
        ) AS live_booked
    FROM parking_slots ps
    ORDER BY ps.slot_number ASC
";
$slotsResult = $conn->query($slotsQuery);

$statsStmt = $conn->prepare("
    SELECT
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_count,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_count,
        COUNT(*) AS total_count
    FROM bookings
    WHERE user_id = ?
");
$statsStmt->bind_param('i', $_SESSION['user_id']);
$statsStmt->execute();
$bookingStats = $statsStmt->get_result()->fetch_assoc();
$statsStmt->close();

$pageTitle = 'User Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">Welcome, <?php echo sanitize($_SESSION['user_name']); ?></h1>
        <p class="text-muted mb-0">Check parking slot status and make a reservation.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>user/history.php" class="btn btn-outline-primary">View Booking History</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Total Bookings</p>
                <h2 class="h3 mb-0"><?php echo (int) ($bookingStats['total_count'] ?? 0); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Active Bookings</p>
                <h2 class="h3 mb-0"><?php echo (int) ($bookingStats['active_count'] ?? 0); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Cancelled Bookings</p>
                <h2 class="h3 mb-0"><?php echo (int) ($bookingStats['cancelled_count'] ?? 0); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h4 mb-1">Parking Slots</h2>
                <p class="text-muted mb-0">Live slot status refreshes automatically every 30 seconds.</p>
            </div>
        </div>
        <div id="slot-grid" class="row g-3" data-live-slots-url="<?php echo BASE_URL; ?>user/live_slots.php">
            <?php while ($slot = $slotsResult->fetch_assoc()): ?>
                <?php
                $isBookable = $slot['status'] === 'available' && (int) $slot['live_booked'] === 0;
                $slotLabel = $isBookable ? 'Available' : ((int) $slot['live_booked'] === 1 ? 'Booked' : ucfirst($slot['status']));
                $slotClass = $isBookable ? 'success' : ((int) $slot['live_booked'] === 1 ? 'danger' : 'warning');
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card slot-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h3 class="h5 mb-1"><?php echo sanitize($slot['slot_number']); ?></h3>
                                    <p class="text-muted mb-0">Parking Slot</p>
                                </div>
                                <span class="badge text-bg-<?php echo $slotClass; ?>"><?php echo sanitize($slotLabel); ?></span>
                            </div>
                            <p class="small text-muted">Rate: Rs. <?php echo PRICE_PER_HOUR; ?>/hour</p>
                            <a href="<?php echo BASE_URL; ?>user/book_slot.php?id=<?php echo (int) $slot['id']; ?>"
                               class="btn btn-primary w-100 <?php echo $isBookable ? '' : 'disabled'; ?>">
                                Book This Slot
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
