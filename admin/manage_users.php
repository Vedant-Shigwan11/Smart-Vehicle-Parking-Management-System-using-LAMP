<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (is_post_request()) {
    $action = $_POST['action'] ?? '';
    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($action === 'delete' && $userId > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            set_flash('success', 'User deleted successfully.');
        } else {
            set_flash('warning', 'User could not be deleted.');
        }

        $stmt->close();
        redirect('admin/manage_users.php');
    }
}

$users = $conn->query("
    SELECT
        u.id,
        u.name,
        u.email,
        u.created_at,
        COUNT(b.id) AS booking_count
    FROM users u
    LEFT JOIN bookings b ON b.user_id = u.id
    WHERE u.role = 'user'
    GROUP BY u.id, u.name, u.email, u.created_at
    ORDER BY u.created_at DESC
");

$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage Users</h1>
        <p class="text-muted mb-0">View registered users and remove unwanted accounts.</p>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered On</th>
                        <th>Total Bookings</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No registered users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo (int) $user['id']; ?></td>
                                <td><?php echo sanitize($user['name']); ?></td>
                                <td><?php echo sanitize($user['email']); ?></td>
                                <td><?php echo sanitize(date('d M Y', strtotime($user['created_at']))); ?></td>
                                <td><?php echo (int) $user['booking_count']; ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Delete this user account?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
