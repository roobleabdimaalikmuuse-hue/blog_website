<?php
/**
 * Kulmiye Blog - Admin Dashboard
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Dashboard';

// Get statistics
$total_posts = count_records($pdo, 'posts', "1=1");
$published_posts = count_records($pdo, 'posts', "status = 'published'");
$draft_posts = count_records($pdo, 'posts', "status = 'draft'");
$total_users = count_records($pdo, 'users', "1=1");
$total_comments = count_records($pdo, 'comments', "1=1");
$pending_comments = count_records($pdo, 'comments', "status = 'pending'");
$approved_comments = count_records($pdo, 'comments', "status = 'approved'");
$total_categories = count_records($pdo, 'categories', "1=1");

// Get recent posts
$recent_posts = $pdo->query("
    SELECT p.*, c.name as category_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
    LIMIT 5
")->fetchAll();

// Get recent comments
$recent_comments = $pdo->query("
    SELECT c.*, u.username, p.title as post_title
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
    LIMIT 5
")->fetchAll();

// Get most viewed posts
$popular_posts = $pdo->query("
    SELECT id, title, views
    FROM posts
    WHERE status = 'published'
    ORDER BY views DESC
    LIMIT 5
")->fetchAll();

include 'header.php';
?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Posts</h6>
                    <h2 class="mb-0 fw-bold"><?php echo $total_posts; ?></h2>
                    <small class="text-success">
                        <i class="bi bi-check-circle"></i> <?php echo $published_posts; ?> published
                    </small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-file-text"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Users</h6>
                    <h2 class="mb-0 fw-bold"><?php echo $total_users; ?></h2>
                    <small class="text-info">
                        <i class="bi bi-people"></i> Registered members
                    </small>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Comments</h6>
                    <h2 class="mb-0 fw-bold"><?php echo $total_comments; ?></h2>
                    <small class="text-warning">
                        <i class="bi bi-clock"></i> <?php echo $pending_comments; ?> pending
                    </small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-chat-dots"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Categories</h6>
                    <h2 class="mb-0 fw-bold"><?php echo $total_categories; ?></h2>
                    <small class="text-secondary">
                        <i class="bi bi-folder"></i> Active categories
                    </small>
                </div>
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-folder"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Quick Actions</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?php echo SITE_URL; ?>/admin/posts.php?action=add" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> New Post
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/users.php" class="btn btn-info">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/comments.php" class="btn btn-warning">
                        <i class="bi bi-chat-dots"></i> Moderate Comments
                        <?php if ($pending_comments > 0): ?>
                            <span class="badge bg-danger"><?php echo $pending_comments; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-secondary">
                        <i class="bi bi-folder"></i> Categories
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Posts -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Recent Posts</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_posts as $post): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/posts.php?action=edit&id=<?php echo $post['id']; ?>">
                                            <?php echo truncate(clean($post['title']), 40); ?>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-secondary"><?php echo clean($post['category_name']); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $post['views']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="<?php echo SITE_URL; ?>/admin/posts.php" class="btn btn-sm btn-outline-primary">
                    View All Posts <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Comments -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Recent Comments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Post</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_comments as $comment): ?>
                                <tr>
                                    <td><?php echo clean($comment['username']); ?></td>
                                    <td><?php echo truncate(clean($comment['post_title']), 30); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $comment['status'] === 'approved' ? 'success' : ($comment['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($comment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/comments.php" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="<?php echo SITE_URL; ?>/admin/comments.php" class="btn btn-sm btn-outline-primary">
                    View All Comments <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Popular Posts -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-fire me-2"></i>Most Viewed Posts</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($popular_posts as $post): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $post['id']; ?>" target="_blank">
                                <?php echo truncate(clean($post['title']), 50); ?>
                            </a>
                            <span class="badge bg-primary rounded-pill"><?php echo $post['views']; ?> views</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>System Information</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>PHP Version:</strong> <?php echo phpversion(); ?>
                    </li>
                    <li class="mb-2">
                        <strong>Database:</strong> MySQL
                    </li>
                    <li class="mb-2">
                        <strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
                    </li>
                    <li class="mb-2">
                        <strong>Blog Name:</strong> Kulmiye
                    </li>
                    <li class="mb-2">
                        <strong>Admin Email:</strong> <?php echo ADMIN_EMAIL; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
