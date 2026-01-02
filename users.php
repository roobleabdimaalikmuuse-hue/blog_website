<?php
/**
 * Kulmiye Blog - Admin Users Management
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Manage Users';

// Handle user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'ban') {
        $pdo->prepare("UPDATE users SET banned = 1 WHERE id = ?")->execute([$user_id]);
        set_flash('success', 'User banned successfully.');
    } elseif ($action === 'unban') {
        $pdo->prepare("UPDATE users SET banned = 0 WHERE id = ?")->execute([$user_id]);
        set_flash('success', 'User unbanned successfully.');
    } elseif ($action === 'delete') {
        // Delete user's comments first
        $pdo->prepare("DELETE FROM comments WHERE user_id = ?")->execute([$user_id]);
        // Delete user
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
        set_flash('success', 'User deleted successfully.');
    }

    redirect('/admin/users.php');
}

// Fetch all users
$stmt = $pdo->query("
    SELECT u.*, 
           COUNT(DISTINCT c.id) as comment_count
    FROM users u
    LEFT JOIN comments c ON u.id = c.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();

include 'header.php';
?>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Users (<?php echo count($users); ?>)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Comments</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($user['profile_image'] && $user['profile_image'] !== 'default.jpg'): ?>
                                        <img src="<?php echo UPLOAD_URL . clean($user['profile_image']); ?>" 
                                             alt="<?php echo clean($user['username']); ?>" 
                                             class="rounded-circle me-2" 
                                             style="width: 32px; height: 32px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px; font-size: 0.875rem;">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php echo clean($user['username']); ?>
                                </div>
                            </td>
                            <td><?php echo clean($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo $user['comment_count']; ?></td>
                            <td><?php echo format_date($user['created_at'], 'M j, Y'); ?></td>
                            <td>
                                <?php if ($user['banned']): ?>
                                    <span class="badge bg-danger">Banned</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($user['banned']): ?>
                                        <a href="?action=unban&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-success" 
                                           title="Unban">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=ban&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-warning" 
                                           title="Ban">
                                            <i class="bi bi-ban"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Delete this user and all their comments?')" 
                                       title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (count($users) === 0): ?>
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted">No users found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
