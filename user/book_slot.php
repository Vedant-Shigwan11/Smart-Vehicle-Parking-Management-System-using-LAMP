<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

if (is_admin()) {
    redirect('admin/dashboard.php');
}

$slotId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($slotId <= 0) {
    set_flash('danger', 'Invalid parking slot selected.');
    redirect('user/dashboard.php');
}

$slotStmt = $conn->prepare('SELECT id, slot_number, status FROM parking_slots WHERE id = ? LIMIT 1');
$slotStmt->bind_param('i', $slotId);
$slotStmt->execute();
$slot = $slotStmt->get_result()->fetch_assoc();
$slotStmt->close();

if (!$slot) {
    set_flash('danger', 'Parking slot not found.');
    redirect('user/dashboard.php');
}

if ($slot['status'] !== 'available') {
    set_flash('warning', 'This slot is currently unavailable for booking.');
    redirect('user/dashboard.php');
}

$errors = [];
$startTime = '';
$endTime = '';
$calculatedPrice = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startTime = trim($_POST['start_time'] ?? '');
    $endTime = trim($_POST['end_time'] ?? '');

    if ($startTime === '' || $endTime === '') {
        $errors[] = 'Start time and end time are required.';
    } else {
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);

        if ($startTimestamp === false || $endTimestamp === false) {
            $errors[] = 'Please choose valid booking times.';
        } elseif ($startTimestamp < time()) {
            $errors[] = 'Start time cannot be in the past.';
        } elseif ($endTimestamp <= $startTimestamp) {
            $errors[] = 'End time must be greater than start time.';
        } else {
            $hours = ($endTimestamp - $startTimestamp) / 3600;
            if ($hours < 1) {
                $errors[] = 'Minimum booking duration is 1 hour.';
            } else {
                $calculatedPrice = calculate_booking_price($startTime, $endTime);
            }
        }
    }

    if (!$errors) {
        $overlapStmt = $conn->prepare("
            SELECT id
            FROM bookings
            WHERE slot_id = ?
              AND status = 'active'
              AND start_time < ?
              AND end_time > ?
            LIMIT 1
        ");
        $overlapStmt->bind_param('iss', $slotId, $endTime, $startTime);
        $overlapStmt->execute();
        $conflict = $overlapStmt->get_result()->fetch_assoc();
        $overlapStmt->close();

        if ($conflict) {
            $errors[] = 'This slot is already booked for the selected time range.';
        } else {
            $bookingStmt = $conn->prepare("
                INSERT INTO bookings (user_id, slot_id, start_time, end_time, price, status)
                VALUES (?, ?, ?, ?, ?, 'active')
            ");
            $bookingStmt->bind_param(
                'iissd',
                $_SESSION['user_id'],
                $slotId,
                $startTime,
                $endTime,
                $calculatedPrice
            );

            if ($bookingStmt->execute()) {
                set_flash('success', 'Slot booked successfully. Total price: Rs. ' . number_format($calculatedPrice, 2));
                $bookingStmt->close();
                redirect('user/history.php');
            }

            $errors[] = 'Unable to complete the booking right now.';
            $bookingStmt->close();
        }
    }
}

$pageTitle = 'Book Parking Slot';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="h3 mb-1">Book Slot <?php echo sanitize($slot['slot_number']); ?></h1>
                        <p class="text-muted mb-0">Parking rate is Rs. <?php echo PRICE_PER_HOUR; ?> per hour.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>user/dashboard.php" class="btn btn-outline-secondary">Back</a>
                </div>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo sanitize($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" id="booking-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Time</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                                   value="<?php echo sanitize($startTime); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                                   value="<?php echo sanitize($endTime); ?>" required>
                        </div>
                    </div>

                    <div class="price-preview mt-3 p-3 rounded">
                        <strong>Estimated Price:</strong>
                        <span id="price-output">Rs. <?php echo number_format($calculatedPrice, 2); ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Confirm Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
