<?php
/**
 * Kulmiye Blog - Admin Categories Management
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Manage Categories';

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = sanitize_input($_POST['name']);
        $description = sanitize_input($_POST['description']);
        
        if (!empty($name)) {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            if ($stmt->execute([$name, $description])) {
                set_flash('success', 'Category added successfully.');
            } else {
                set_flash('error', 'Failed to add category.');
            }
            redirect('/admin/categories.php');
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $category_id = (int)$_GET['id'];
    
    // Check if category has posts
    $count = count_records($pdo, 'posts', "category_id = $category_id");
    
    if ($count > 0) {
        set_flash('error', "Cannot delete category with $count post(s). Reassign posts first.");
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$category_id]);
        set_flash('success', 'Category deleted successfully.');
    }
    
    redirect('/admin/categories.php');
}

// Fetch all categories with post count
$stmt = $pdo->query("
    SELECT c.*, COUNT(p.id) as post_count
    FROM categories c
    LEFT JOIN posts p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name
");
$categories = $stmt->fetchAll();

include 'header.php';
?>

<div class="row">
    <!-- Add Category Form -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Add New Category</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="add_category" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Category
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Categories List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">All Categories (<?php echo count($categories); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Posts</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td>
                                        <strong><?php echo clean($category['name']); ?></strong>
                                    </td>
                                    <td><?php echo truncate(clean($category['description'] ?? ''), 50); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $category['post_count']; ?></span>
                                    </td>
                                    <td><?php echo format_date($category['created_at'], 'M j, Y'); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo SITE_URL; ?>/public/category.php?id=<?php echo $category['id']; ?>" 
                                               class="btn btn-info" 
                                               target="_blank" 
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="?action=delete&id=<?php echo $category['id']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirm('Delete this category?')" 
                                               title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if (count($categories) === 0): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-folder fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted">No categories found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
