<?php
/**
 * Kulmiye Blog - Single Post View
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$post_id) {
    redirect('/public/index.php');
}

// Fetch post details
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name,
           CASE 
               WHEN p.author_type = 'admin' THEN a.username
               ELSE u.username
           END as author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN admins a ON p.author_id = a.id AND p.author_type = 'admin'
    LEFT JOIN users u ON p.author_id = u.id AND p.author_type = 'user'
    WHERE p.id = ? AND p.status = 'published'
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('error', 'Post not found.');
    redirect('/public/index.php');
}

// Increment view count
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post_id]);

// Fetch post tags
$tags_stmt = $pdo->prepare("
    SELECT t.* FROM tags t
    JOIN post_tags pt ON t.id = pt.tag_id
    WHERE pt.post_id = ?
");
$tags_stmt->execute([$post_id]);
$tags = $tags_stmt->fetchAll();

// Fetch approved comments
$comments_stmt = $pdo->prepare("
    SELECT c.*, u.username, u.profile_image
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id = ? AND c.status = 'approved'
    ORDER BY c.created_at DESC
");
$comments_stmt->execute([$post_id]);
$comments = $comments_stmt->fetchAll();

// Fetch related posts (same category)
$related_stmt = $pdo->prepare("
    SELECT id, title, thumbnail, created_at
    FROM posts
    WHERE category_id = ? AND id != ? AND status = 'published'
    ORDER BY created_at DESC
    LIMIT 3
");
$related_stmt->execute([$post['category_id'], $post_id]);
$related_posts = $related_stmt->fetchAll();

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    require_login();
    
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $comment_content = sanitize_input($_POST['comment_content'] ?? '');
        
        if (!empty($comment_content)) {
            $insert_stmt = $pdo->prepare("
                INSERT INTO comments (post_id, user_id, content, status)
                VALUES (?, ?, ?, 'approved')
            ");
            
            if ($insert_stmt->execute([$post_id, $_SESSION['user_id'], $comment_content])) {
                set_flash('success', 'Comment posted successfully!');
                redirect('/public/post.php?id=' . $post_id);
            }
        }
    }
}

$page_title = $post['title'];
$meta_description = truncate(strip_tags($post['content']), 160);
$meta_keywords = implode(', ', array_column($tags, 'name'));

include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <article class="post-content">
                <div class="post-header">
                    <a href="<?php echo SITE_URL; ?>/public/category.php?id=<?php echo $post['category_id']; ?>" 
                       class="badge badge-primary mb-3">
                        <?php echo clean($post['category_name']); ?>
                    </a>
                    
                    <h1 class="post-title"><?php echo clean($post['title']); ?></h1>
                    
                    <div class="post-meta">
                        <span class="post-meta-item">
                            <i class="bi bi-eye"></i>
                            <?php echo $post['views']; ?> views
                        </span>
                    </div>
                </div>

                <?php if ($post['thumbnail']): ?>
                    <img src="<?php echo UPLOAD_URL . clean($post['thumbnail']); ?>" 
                         alt="<?php echo clean($post['title']); ?>" 
                         class="post-thumbnail">
                <?php endif; ?>

                <div class="post-body">
                    <?php echo $post['content']; ?>
                </div>

                <?php if (count($tags) > 0): ?>
                    <div class="post-tags mt-4">
                        <h6 class="d-inline me-2"><i class="bi bi-tags"></i> Tags:</h6>
                        <?php foreach ($tags as $tag): ?>
                            <span class="badge bg-secondary me-1"><?php echo clean($tag['name']); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>

            <!-- Comments Section -->
            <div class="comments-section mt-5">
                <h3 class="mb-4">
                    <i class="bi bi-chat-left-text"></i> 
                    Comments (<?php echo count($comments); ?>)
                </h3>

                <?php if (is_logged_in()): ?>
                    <form method="POST" class="mb-5">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group mb-3">
                            <label for="comment_content" class="form-label">Leave a Comment</label>
                            <textarea class="form-control" 
                                      id="comment_content" 
                                      name="comment_content" 
                                      rows="4" 
                                      required 
                                      placeholder="Share your thoughts..."></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="btn btn-primary">
                            <i class="bi bi-send"></i> Post Comment
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        Please <a href="<?php echo SITE_URL; ?>/public/login.php">login</a> to leave a comment.
                    </div>
                <?php endif; ?>

                <div id="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <span class="comment-author">
                                    <i class="bi bi-person-circle"></i> 
                                    <?php echo clean($comment['username']); ?>
                                </span>
                                <span class="comment-date">
                                    <?php echo time_ago($comment['created_at']); ?>
                                </span>
                            </div>
                            <div class="comment-body">
                                <?php echo nl2br(clean($comment['content'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($comments) === 0): ?>
                        <p class="text-muted text-center py-4">
                            <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                            No comments yet. Be the first to comment!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related Posts -->
            <?php if (count($related_posts) > 0): ?>
                <div class="sidebar-widget">
                    <h5><i class="bi bi-collection me-2"></i>Related Posts</h5>
                    <ul>
                        <?php foreach ($related_posts as $related): ?>
                            <li>
                                <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $related['id']; ?>">
                                    <?php echo clean($related['title']); ?>
                                </a>

                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Share Widget -->
            <div class="sidebar-widget">
                <h5><i class="bi bi-share me-2"></i>Share This Post</h5>
                <div class="d-flex gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/public/post.php?id=' . $post_id); ?>" 
                       target="_blank" 
                       class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/public/post.php?id=' . $post_id); ?>&text=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" 
                       class="btn btn-outline-info btn-sm flex-fill">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL . '/public/post.php?id=' . $post_id); ?>" 
                       target="_blank" 
                       class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
