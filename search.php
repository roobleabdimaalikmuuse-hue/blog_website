<?php
/**
 * Kulmiye Blog - Search Results
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

$query = isset($_GET['q']) ? sanitize_input($_GET['q']) : '';
$posts = [];
$total_results = 0;

if (!empty($query)) {
    // Use FULLTEXT search
    $search_term = '%' . $query . '%';
    
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
        WHERE p.status = 'published' 
        AND (p.title LIKE ? OR p.content LIKE ?)
        ORDER BY p.created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$search_term, $search_term]);
    $posts = $stmt->fetchAll();
    $total_results = count($posts);
}

$page_title = 'Search Results';
$meta_description = 'Search results for: ' . $query;

include '../includes/header.php';
?>

<div class="container my-5">
    <!-- Search Header -->
    <div class="text-center mb-5">
        <h1 class="display-5">Search Results</h1>
        <?php if (!empty($query)): ?>
            <p class="lead text-muted">
                Found <strong><?php echo $total_results; ?></strong> result<?php echo $total_results != 1 ? 's' : ''; ?> 
                for "<strong><?php echo clean($query); ?></strong>"
            </p>
        <?php endif; ?>
    </div>

    <!-- Search Form -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-6">
            <form method="GET" action="<?php echo SITE_URL; ?>/public/search.php">
                <div class="input-group input-group-lg">
                    <input type="text" 
                           class="form-control" 
                           name="q" 
                           placeholder="Search articles..." 
                           value="<?php echo clean($query); ?>"
                           required>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <?php if (!empty($query)): ?>
        <?php if (count($posts) > 0): ?>
            <div class="row g-4">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="card h-100">
                            <?php if ($post['thumbnail']): ?>
                                <img src="<?php echo UPLOAD_URL . clean($post['thumbnail']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo clean($post['title']); ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-gradient" style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                            <?php endif; ?>
                            <div class="card-body">
                                <a href="<?php echo SITE_URL; ?>/public/category.php?id=<?php echo $post['category_id']; ?>" 
                                   class="badge badge-category mb-2">
                                    <?php echo clean($post['category_name']); ?>
                                </a>
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
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                <h4 class="text-muted">No results found</h4>
                <p class="text-muted">Try different keywords or browse our categories</p>
                <a href="<?php echo SITE_URL; ?>/public/index.php" class="btn btn-primary mt-3">
                    <i class="bi bi-house"></i> Back to Home
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
