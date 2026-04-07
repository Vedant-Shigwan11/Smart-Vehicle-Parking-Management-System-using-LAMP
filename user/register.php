<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    redirect('user/dashboard.php');
}

$errors = [];
$name = '';
$email = '';

if (is_post_request()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password confirmation does not match.';
    }

    if (!$errors) {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $existingUser = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($existingUser) {
            $errors[] = 'This email address is already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $role = 'user';
            $insertStmt->bind_param('ssss', $name, $email, $hashedPassword, $role);

            if ($insertStmt->execute()) {
                set_flash('success', 'Registration successful. Please log in.');
                $insertStmt->close();
                redirect('user/login.php');
            }

            $errors[] = 'Unable to create your account right now.';
            $insertStmt->close();
        }
    }
}

$pageTitle = 'User Registration';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h3 mb-3">Create your account</h1>
                <p class="text-muted">Register to reserve parking slots online.</p>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo sanitize($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo sanitize($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo sanitize($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>

                <p class="mt-3 mb-0 text-center">
                    Already have an account?
                    <a href="<?php echo BASE_URL; ?>user/login.php">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
