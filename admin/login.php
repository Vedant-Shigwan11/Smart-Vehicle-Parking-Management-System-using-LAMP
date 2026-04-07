<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (is_admin()) {
    redirect('admin/dashboard.php');
}

$errors = [];
$email = '';

if (is_post_request()) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (!$errors) {
        $stmt = $conn->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? AND role = ? LIMIT 1');
        $role = 'admin';
        $stmt->bind_param('ss', $email, $role);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password'])) {
            login_user($admin);
            set_flash('success', 'Admin login successful.');
            redirect('admin/dashboard.php');
        }

        $errors[] = 'Invalid admin credentials.';
    }
}

$pageTitle = 'Admin Login';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h3 mb-3">Admin Login</h1>
                <p class="text-muted">Use the default admin account after importing the SQL file.</p>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo sanitize($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info small">
                    Default admin login: <strong>admin@smartparking.local</strong><br>
                    Password: <strong>admin123</strong>
                </div>

                <form method="POST" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo sanitize($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Login as Admin</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
