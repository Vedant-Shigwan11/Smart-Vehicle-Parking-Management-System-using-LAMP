<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$errors = [];
$editSlot = null;

if (is_post_request()) {
    $action = $_POST['action'] ?? '';
    $slotNumber = strtoupper(trim($_POST['slot_number'] ?? ''));
    $status = $_POST['status'] ?? 'available';
    $allowedStatuses = ['available', 'maintenance'];

    if (!in_array($status, $allowedStatuses, true)) {
        $errors[] = 'Invalid slot status selected.';
    }

    if ($action !== 'delete' && $slotNumber === '') {
        $errors[] = 'Slot number is required.';
    }

    if (!$errors) {
        if ($action === 'create') {
            $stmt = $conn->prepare('INSERT INTO parking_slots (slot_number, status) VALUES (?, ?)');
            $stmt->bind_param('ss', $slotNumber, $status);

            if ($stmt->execute()) {
                set_flash('success', 'Parking slot added successfully.');
                $stmt->close();
                redirect('admin/manage_slots.php');
            }

            $errors[] = 'Unable to add parking slot. Make sure the slot number is unique.';
            $stmt->close();
        }

        if ($action === 'update') {
            $slotId = (int) ($_POST['slot_id'] ?? 0);
            $stmt = $conn->prepare('UPDATE parking_slots SET slot_number = ?, status = ? WHERE id = ?');
            $stmt->bind_param('ssi', $slotNumber, $status, $slotId);

            if ($stmt->execute()) {
                set_flash('success', 'Parking slot updated successfully.');
                $stmt->close();
                redirect('admin/manage_slots.php');
            }

            $errors[] = 'Unable to update parking slot.';
            $stmt->close();
        }

        if ($action === 'delete') {
            $slotId = (int) ($_POST['slot_id'] ?? 0);
            $stmt = $conn->prepare('DELETE FROM parking_slots WHERE id = ?');
            $stmt->bind_param('i', $slotId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                set_flash('success', 'Parking slot deleted successfully.');
            } else {
                set_flash('warning', 'Parking slot could not be deleted.');
            }

            $stmt->close();
            redirect('admin/manage_slots.php');
        }
    }
}

if (isset($_GET['edit'])) {
    $slotId = (int) $_GET['edit'];
    $editStmt = $conn->prepare('SELECT id, slot_number, status FROM parking_slots WHERE id = ? LIMIT 1');
    $editStmt->bind_param('i', $slotId);
    $editStmt->execute();
    $editSlot = $editStmt->get_result()->fetch_assoc();
    $editStmt->close();
}

$slots = $conn->query('SELECT id, slot_number, status FROM parking_slots ORDER BY slot_number ASC');

$pageTitle = 'Manage Parking Slots';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h1 class="h4 mb-3"><?php echo $editSlot ? 'Edit Slot' : 'Add New Slot'; ?></h1>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo sanitize($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editSlot ? 'update' : 'create'; ?>">
                    <?php if ($editSlot): ?>
                        <input type="hidden" name="slot_id" value="<?php echo (int) $editSlot['id']; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Slot Number</label>
                        <input type="text" name="slot_number" class="form-control"
                               value="<?php echo sanitize($editSlot['slot_number'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="available" <?php echo (($editSlot['status'] ?? '') === 'available') ? 'selected' : ''; ?>>Available</option>
                            <option value="maintenance" <?php echo (($editSlot['status'] ?? '') === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><?php echo $editSlot ? 'Update Slot' : 'Add Slot'; ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">All Parking Slots</h2>
                    <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Slot Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($slots->num_rows === 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No parking slots found.</td>
                                </tr>
                            <?php else: ?>
                                <?php while ($slot = $slots->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo (int) $slot['id']; ?></td>
                                        <td><?php echo sanitize($slot['slot_number']); ?></td>
                                        <td>
                                            <span class="badge text-bg-<?php echo slot_status_badge($slot['status']); ?>">
                                                <?php echo sanitize(ucfirst($slot['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <a href="<?php echo BASE_URL; ?>admin/manage_slots.php?edit=<?php echo (int) $slot['id']; ?>"
                                               class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="POST" onsubmit="return confirm('Delete this parking slot?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="slot_id" value="<?php echo (int) $slot['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
