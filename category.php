<?php
/**
 * Kulmiye Blog - Category Posts
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$category_id) {
    redirect('/public/index.php');
}

// Fetch category details
$cat_stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$cat_stmt->execute([$category_id]);
$category = $cat_stmt->fetch();

if (!$category) {
    set_flash('error', 'Category not found.');
    redirect('/public/index.php');
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_posts = count_records($pdo, 'posts', "category_id = $category_id AND status = 'published'");
$pagination = paginate($total_posts, POSTS_PER_PAGE, $page);

// Fetch posts in this category
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
    WHERE p.category_id = ? AND p.status = 'published'
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$category_id, POSTS_PER_PAGE, $pagination['offset']]);
$posts = $stmt->fetchAll();

$page_title = $category['name'];
$meta_description = $category['description'] ?? 'Browse articles in ' . $category['name'];

include '../includes/header.php';
?>

<div class="container my-5">
    <!-- Category Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 text-gradient"><?php echo clean($category['name']); ?></h1>
        <?php if ($category['description']): ?>
            <p class="lead text-muted"><?php echo clean($category['description']); ?></p>
        <?php endif; ?>
        <p class="text-muted">
            <i class="bi bi-file-text"></i> <?php echo $total_posts; ?> article<?php echo $total_posts != 1 ? 's' : ''; ?>
        </p>
    </div>

    <!-- Posts Grid -->
    <div class="row g-4">
        <?php foreach ($posts as $post): ?>
            <div class="col-md-6 col-lg-4">
                <article class="card h-100 fade-in">
                    <?php if ($post['thumbnail']): ?>
                        <img src="<?php echo UPLOAD_URL . clean($post['thumbnail']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo clean($post['title']); ?>">
                    <?php else: ?>
                        <div class="card-img-top bg-gradient" style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $post['id']; ?>">
                                <?php echo clean($post['title']); ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <?php echo truncate(strip_tags($post['content']), 120); ?>
                        </p>

                        <a href="<?php echo SITE_URL; ?>/public/post.php?id=<?php echo $post['id']; ?>" 
                           class="btn btn-outline-primary btn-sm mt-3">
                            Read More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($posts) === 0): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
            <h4 class="text-muted">No posts in this category yet.</h4>
            <a href="<?php echo SITE_URL; ?>/public/index.php" class="btn btn-primary mt-3">
                <i class="bi bi-house"></i> Back to Home
            </a>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="mt-5">
            <?php echo pagination_html($pagination, SITE_URL . '/public/category.php?id=' . $category_id); ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
