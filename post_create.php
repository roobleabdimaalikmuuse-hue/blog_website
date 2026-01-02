<?php
/**
 * Kulmiye Blog - Create Post
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Create Post';
$errors = [];

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $title = sanitize_input($_POST['title'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $content = $_POST['content'] ?? ''; // Content from WYSIWYG, allowed raw
        $excerpt = sanitize_input($_POST['excerpt'] ?? '');
        $status = sanitize_input($_POST['status'] ?? 'draft');
        
        // Validation
        if (empty($title)) $errors[] = 'Title is required.';
        if (empty($content)) $errors[] = 'Content is required.';
        if (!$category_id) $errors[] = 'Category is required.';
        
        // Handle Thumbnail Upload
        $thumbnail = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $upload_error = '';
            $thumbnail = upload_image($_FILES['thumbnail'], 'blog', $upload_error);
            if (!$thumbnail) {
                $errors[] = 'Failed to upload thumbnail: ' . $upload_error;
            }
        }
        
        if (empty($errors)) {
            // Generate Slug
            $slug = generate_slug($title);
            
            // Generate Excerpt if empty
            if (empty($excerpt)) {
                $excerpt = truncate(strip_tags($content), 150, '');
            }
            
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO posts (title, slug, content, excerpt, category_id, author_id, author_type, thumbnail, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'admin', ?, ?)
                ");
                
                $stmt->execute([
                    $title, 
                    $slug . '-' . uniqid(), // Ensure uniqueness
                    $content, 
                    $excerpt, 
                    $category_id, 
                    $_SESSION['admin_id'],
                    $thumbnail, 
                    $status
                ]);
                
                set_flash('success', 'Post created successfully!');
                redirect('/admin/posts.php');
            } catch (PDOException $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    } else {
        $errors[] = 'Invalid security token.';
    }
}

include 'header.php';
?>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Create New Post</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo clean($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title</label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="<?php echo isset($_POST['title']) ? clean($_POST['title']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="20">
                            <?php echo isset($_POST['content']) ? clean($_POST['content']) : ''; ?>
                        </textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt (Short Summary)</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo isset($_POST['excerpt']) ? clean($_POST['excerpt']) : ''; ?></textarea>
                        <small class="text-muted">Optional. Leave empty to auto-generate from content.</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Publishing</h6>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo clean($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-cloud-upload"></i> Save Post
                            </button>
                        </div>
                    </div>
                    
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="card-title">Thumbnail</h6>
                            <input type="file" class="form-control" name="thumbnail" accept="image/*">
                            <small class="text-muted d-block mt-2">Recommended size: 1200x630px</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 500,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        images_upload_url: 'upload_image.php',
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: (cb, value, meta) => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.addEventListener('change', (e) => {
                const file = e.target.files[0];

                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    const id = 'blobid' + (new Date()).getTime();
                    const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    const base64 = reader.result.split(',')[1];
                    const blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    /* call the callback and populate the Title field with the file name */
                    cb(blobInfo.blobUri(), { title: file.name });
                });
                reader.readAsDataURL(file);
            });

            input.click();
        },
    });
</script>

<?php include 'footer.php'; ?>
