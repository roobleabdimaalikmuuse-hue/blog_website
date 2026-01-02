<?php
/**
 * Kulmiye Blog - Admin Comments Management
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Manage Comments';

// Handle comment actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $comment_id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$comment_id]);
        set_flash('success', 'Comment approved successfully.');
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?")->execute([$comment_id]);
        set_flash('success', 'Comment rejected.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$comment_id]);
        set_flash('success', 'Comment deleted successfully.');
    }

    redirect('/admin/comments.php');
}

// Fetch all comments
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = $filter === 'all' ? "1=1" : "c.status = '$filter'";

$stmt = $pdo->query("
    SELECT c.*, u.username, p.title as post_title, p.id as post_id
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    WHERE $where
    ORDER BY c.created_at DESC
");
$comments = $stmt->fetchAll();

include 'header.php';
?>

<!-- Filter Tabs -->
<div class="mb-4">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" 
               href="?filter=all">All Comments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $filter === 'pending' ? 'active' : ''; ?>" 
               href="?filter=pending">
                Pending
                <?php
                $pending_count = count_records($pdo, 'comments', "status = 'pending'");
                if ($pending_count > 0):
                ?>
                    <span class="badge bg-warning"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $filter === 'approved' ? 'active' : ''; ?>" 
               href="?filter=approved">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $filter === 'rejected' ? 'active' : ''; ?>" 
               href="?filter=rejected">Rejected</a>
        </li>
    </ul>
</div>

<!-- Comments Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Post</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><?php echo $comment['id']; ?></td>
                            <td><?php echo clean($comment['username']); ?></td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $comment['post_id']; ?>" target="_blank">
                                    <?php echo truncate(clean($comment['post_title']), 30); ?>
                                </a>
                            </td>
                            <td><?php echo truncate(clean($comment['content']), 50); ?></td>
                            <td><?php echo time_ago($comment['created_at']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $comment['status'] === 'approved' ? 'success' : ($comment['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($comment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($comment['status'] !== 'approved'): ?>
                                        <a href="?action=approve&id=<?php echo $comment['id']; ?>" 
                                           class="btn btn-success" 
                                           title="Approve">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($comment['status'] !== 'rejected'): ?>
                                        <a href="?action=reject&id=<?php echo $comment['id']; ?>" 
                                           class="btn btn-warning" 
                                           title="Reject">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $comment['id']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Delete this comment?')" 
                                       title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (count($comments) === 0): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted">No comments found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
