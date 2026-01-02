<?php
/**
 * Kulmiye Blog - User Registration
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/public/index.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $username = sanitize_input($_POST['username'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters long.';
        }

        if (!validate_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (!validate_password($password)) {
            $errors[] = 'Password must be at least 8 characters and contain letters and numbers.';
        }

        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }

        // Check if username exists
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Username already taken.';
            }
        }

        // Check if email exists
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email already registered.';
            }
        }

        // Handle profile image upload
        $profile_image = 'default.jpg';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = upload_image($_FILES['profile_image'], 'profile');
            if ($uploaded) {
                $profile_image = $uploaded;
            }
        }

        // Register user
        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, profile_image)
                VALUES (?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$username, $email, $hashed_password, $profile_image])) {
                set_flash('success', 'Registration successful! Please login.');
                redirect('/public/login.php');
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

$page_title = 'Register';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Kulmiye</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="bi bi-journal-text"></i> Kulmiye</h2>
                <p class="text-muted">Create your account</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo clean($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           value="<?php echo isset($_POST['username']) ? clean($_POST['username']) : ''; ?>"
                           required 
                           minlength="3">
                    <div class="invalid-feedback">Please enter a username (min 3 characters).</div>
                </div>

                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? clean($_POST['email']) : ''; ?>"
                           required>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required 
                           minlength="8">
                    <div class="invalid-feedback">Password must be at least 8 characters.</div>
                </div>

                <div class="form-group mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required>
                    <div class="invalid-feedback">Please confirm your password.</div>
                </div>

                <div class="form-group mb-4">
                    <label for="profile_image" class="form-label">Profile Image (Optional)</label>
                    <input type="file" 
                           class="form-control" 
                           id="profile_image" 
                           name="profile_image" 
                           accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>

                <div class="text-center">
                    <p class="text-muted mb-0">
                        Already have an account? 
                        <a href="<?php echo SITE_URL; ?>/public/login.php" class="text-decoration-none">Login here</a>
                    </p>
                    <p class="text-muted mb-0 mt-2">
                        <a href="<?php echo SITE_URL; ?>/public/index.php" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Back to Website
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html>
