<?php
/**
 * Kulmiye Blog - User Profile
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_login();

$user = fetch_current_user($pdo);
$success = '';
$errors = [];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $username = sanitize_input($_POST['username'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');

        // Validation
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters.';
        }

        if (!validate_email($email)) {
            $errors[] = 'Invalid email address.';
        }

        // Check username uniqueness
        if (empty($errors) && $username !== $user['username']) {
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $check_stmt->execute([$username, $user['id']]);
            if ($check_stmt->fetch()) {
                $errors[] = 'Username already taken.';
            }
        }

        // Check email uniqueness
        if (empty($errors) && $email !== $user['email']) {
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check_stmt->execute([$email, $user['id']]);
            if ($check_stmt->fetch()) {
                $errors[] = 'Email already registered.';
            }
        }

        // Handle profile image upload
        $profile_image = $user['profile_image'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = upload_image($_FILES['profile_image'], 'profile');
            if ($uploaded) {
                // Delete old image if not default
                if ($user['profile_image'] !== 'default.jpg') {
                    delete_image($user['profile_image']);
                }
                $profile_image = $uploaded;
            }
        }

        // Update profile
        if (empty($errors)) {
            $update_stmt = $pdo->prepare("
                UPDATE users 
                SET username = ?, email = ?, profile_image = ?
                WHERE id = ?
            ");
            
            if ($update_stmt->execute([$username, $email, $profile_image, $user['id']])) {
                set_flash('success', 'Profile updated successfully!');
                redirect('/public/profile.php');
            } else {
                $errors[] = 'Failed to update profile.';
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        }

        if (!validate_password($new_password)) {
            $errors[] = 'New password must be at least 8 characters with letters and numbers.';
        }

        if ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match.';
        }

        if (empty($errors)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            
            if ($update_stmt->execute([$hashed_password, $user['id']])) {
                set_flash('success', 'Password changed successfully!');
                redirect('/public/profile.php');
            } else {
                $errors[] = 'Failed to change password.';
            }
        }
    }
}

// Fetch user's comments
$comments_stmt = $pdo->prepare("
    SELECT c.*, p.title as post_title, p.id as post_id
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
    LIMIT 10
");
$comments_stmt->execute([$user['id']]);
$user_comments = $comments_stmt->fetchAll();

$page_title = 'My Profile';

include '../includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-person-circle"></i> My Profile</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo clean($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Profile Information</h5>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="text-center mb-4">
                            <?php if ($user['profile_image'] && $user['profile_image'] !== 'default.jpg'): ?>
                                <img src="<?php echo UPLOAD_URL . clean($user['profile_image']); ?>" 
                                     alt="Profile" 
                                     class="rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo clean($user['username']); ?>" 
                                   required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo clean($user['email']); ?>" 
                                   required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="profile_image" class="form-label">Change Profile Image</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="profile_image" 
                                   name="profile_image" 
                                   accept="image/*">
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Change Password</h5>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="form-group mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                        </div>

                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="bi bi-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Account Stats</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-calendar-check text-primary"></i>
                            <strong>Member since:</strong> <?php echo format_date($user['created_at']); ?>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-chat-dots text-primary"></i>
                            <strong>Comments:</strong> <?php echo count($user_comments); ?>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Comments -->
            <?php if (count($user_comments) > 0): ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Recent Comments</h5>
                        <ul class="list-unstyled">
                            <?php foreach ($user_comments as $comment): ?>
                                <li class="mb-3 pb-3 border-bottom">
                                    <small class="text-muted d-block mb-1">
                                        On: <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $comment['post_id']; ?>">
                                            <?php echo clean($comment['post_title']); ?>
                                        </a>
                                    </small>
                                    <p class="mb-1 small"><?php echo truncate(clean($comment['content']), 80); ?></p>
                                    <small class="text-muted">
                                        <span class="badge bg-<?php echo $comment['status'] === 'approved' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($comment['status']); ?>
                                        </span>
                                        <?php echo time_ago($comment['created_at']); ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
