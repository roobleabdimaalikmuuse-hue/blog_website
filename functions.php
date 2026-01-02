<?php
/**
 * Kulmiye Blog System - Helper Functions
 * 
 * Reusable utility functions for security, validation, and common operations
 */

/**
 * Sanitize output to prevent XSS attacks
 */
function clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to a specific page
 */
function redirect($path) {
    header("Location: " . SITE_URL . $path);
    exit();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

/**
 * Require user login
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = "Please login to access this page.";
        redirect('/public/login.php');
    }
}

/**
 * Require admin login
 */
function require_admin() {
    if (!is_admin_logged_in()) {
        $_SESSION['error'] = "Unauthorized access. Admin login required.";
        redirect('/public/login.php');
    }
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear flash message
 */
function get_flash($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

/**
 * Validate email format
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate password strength
 */
function validate_password($password) {
    // Minimum 8 characters, at least one letter and one number
    return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password);
}

/**
 * Generate URL-friendly slug
 */
function generate_slug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Format date for display
 */
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Time ago function
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    $periods = [
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    ];
    
    foreach ($periods as $key => $value) {
        if ($difference >= $value) {
            $time = floor($difference / $value);
            return $time . ' ' . $key . ($time > 1 ? 's' : '') . ' ago';
        }
    }
    
    return 'just now';
}

/**
 * Truncate text
 */
function truncate($text, $length = 150, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Upload image file
 */
function upload_image($file, $prefix = 'img', &$error_msg = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $error_msg = 'Upload error: ' . ($file['error'] ?? 'Unknown error');
        return false;
    }
    
    // Validate file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $error_msg = 'File size exceeds limit of ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB';
        return false;
    }
    
    // Validate file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $error_msg = 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS);
        return false;
    }
    
    // Generate unique filename
    $filename = $prefix . '_' . uniqid() . '.' . $extension;
    $destination = UPLOAD_PATH . $filename;
    
    // Create upload directory if it doesn't exist
    if (!is_dir(UPLOAD_PATH)) {
        if (!mkdir(UPLOAD_PATH, 0755, true)) {
            $error_msg = 'Failed to create upload directory.';
            return false;
        }
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    
    $error_msg = 'Failed to move uploaded file.';
    return false;
}

/**
 * Delete image file
 */
function delete_image($filename) {
    if (empty($filename) || $filename === 'default.jpg') {
        return false;
    }
    
    $filepath = UPLOAD_PATH . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

/**
 * Get current user data
 */
function fetch_current_user($pdo) {
    if (!is_logged_in()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Get current admin data
 */
function get_current_admin($pdo) {
    if (!is_admin_logged_in()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

/**
 * Count total records
 */
function count_records($pdo, $table, $where = '') {
    $sql = "SELECT COUNT(*) FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

/**
 * Pagination helper
 */
function paginate($total, $per_page, $current_page = 1) {
    $total_pages = ceil($total / $per_page);
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

/**
 * Generate pagination HTML
 */
function pagination_html($pagination, $base_url) {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($pagination['has_prev']) {
        $prev_page = $pagination['current_page'] - 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $prev_page . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        if ($i == $pagination['current_page']) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Next button
    if ($pagination['has_next']) {
        $next_page = $pagination['current_page'] + 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $next_page . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}
