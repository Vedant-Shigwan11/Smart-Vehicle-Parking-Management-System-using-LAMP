<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

header('Content-Type: application/json');

$slots = [];
$query = "
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

$result = $conn->query($query);
while ($slot = $result->fetch_assoc()) {
    $isBookable = $slot['status'] === 'available' && (int) $slot['live_booked'] === 0;
    $slots[] = [
        'id' => (int) $slot['id'],
        'slot_number' => $slot['slot_number'],
        'status_label' => $isBookable ? 'Available' : ((int) $slot['live_booked'] === 1 ? 'Booked' : ucfirst($slot['status'])),
        'status_class' => $isBookable ? 'success' : ((int) $slot['live_booked'] === 1 ? 'danger' : 'warning'),
        'is_bookable' => $isBookable,
    ];
}

echo json_encode(['slots' => $slots]);
