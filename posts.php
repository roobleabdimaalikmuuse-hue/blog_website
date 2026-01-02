<?php
/**
 * Kulmiye Blog - Admin Posts Management
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Manage Posts';

// Handle post deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    
    // Get post to delete thumbnail
    $post = $pdo->prepare("SELECT thumbnail FROM posts WHERE id = ?");
    $post->execute([$post_id]);
    $post_data = $post->fetch();
    
    if ($post_data && $post_data['thumbnail']) {
        delete_image($post_data['thumbnail']);
    }
    
    // Delete post (comments will be deleted via CASCADE)
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$post_id]);
    set_flash('success', 'Post deleted successfully.');
    redirect('/admin/posts.php');
}

// Handle post status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status' && isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    $pdo->query("UPDATE posts SET status = IF(status = 'published', 'draft', 'published') WHERE id = $post_id");
    set_flash('success', 'Post status updated.');
    redirect('/admin/posts.php');
}

// Fetch all posts
$stmt = $pdo->query("
    SELECT p.*, c.name as category_name,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll();

include 'header.php';
?>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Posts (<?php echo count($posts); ?>)</h5>
        <a href="post_create.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add New Post
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Comments</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $post['id']; ?>" target="_blank">
                                    <?php echo truncate(clean($post['title']), 50); ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo clean($post['category_name']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $post['views']; ?></td>
                            <td><?php echo $post['comment_count']; ?></td>
                            <td><?php echo format_date($post['created_at'], 'M j, Y'); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?action=toggle_status&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-<?php echo $post['status'] === 'published' ? 'warning' : 'success'; ?>" 
                                       title="<?php echo $post['status'] === 'published' ? 'Unpublish' : 'Publish'; ?>">
                                        <i class="bi bi-<?php echo $post['status'] === 'published' ? 'eye-slash' : 'eye'; ?>"></i>
                                    </a>
                                    <a href="post_edit.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-primary" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-info" 
                                       target="_blank" 
                                       title="View">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="?action=delete&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Delete this post?')" 
                                       title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (count($posts) === 0): ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-text fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted">No posts found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<?php include 'footer.php'; ?>
