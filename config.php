<?php
/**
 * Kulmiye Blog System - Database Configuration
 * 
 * Supports both Localhost (XAMPP) and Online Server automatically.
 */

// 1. Detect Environment (Localhost vs Online)
// We check if the server name is localhost, 127.0.0.1, or starts with 192.168. (Local Network)
$is_local = (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['SERVER_NAME'] === '127.0.0.1' ||
    strpos($_SERVER['SERVER_NAME'], '192.168.') === 0
);

// 2. Database Credentials
if ($is_local) {
    // === LOCALHOST SETTINGS (XAMPP) ===
    // Markaad PC-gaaga joogto, halkan ayuu isticmaalayaa
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'blog_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // === ONLINE SERVER SETTINGS (InfinityFree) ===
    // Markaad website-ka upload-gareyso, halkan ayuu isticmaalayaa
    define('DB_HOST', 'sql213.infinityfree.com');
    define('DB_NAME', 'if0_40803727_blog_db');
    define('DB_USER', 'if0_40803727');
    define('DB_PASS', 'kulmiye1122');
}

define('DB_CHARSET', 'utf8mb4');

// 3. Site URL Configuration (Auto-Detect)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Remove default ports for cleaner URLs
$host = str_replace(':80', '', $host);
$host = str_replace(':443', '', $host);

// Determine valid root path
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$path_parts = explode('/', trim($script_name, '/'));
$root_folder = '';

if ($is_local) {
    if (!empty($path_parts)) {
        $root_folder = '/' . $path_parts[0]; // e.g., /Blog_website
    } else {
        $root_folder = '/Blog_website';
    }
} else {
    // Online root path
    $root_folder = '';
}

// Define the SITE_URL properly
define('SITE_URL', $protocol . $host . $root_folder);
define('SITE_NAME', 'Kulmiye');
define('ADMIN_EMAIL', 'admin@kulmiye.com');

// 4. Upload Paths
define('UPLOAD_PATH', dirname(__DIR__) . '/assets/images/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/images/uploads/');

define('MAX_FILE_SIZE', 52428800); // 50MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// 5. Pagination
define('POSTS_PER_PAGE', 10);

// 6. Session Handling
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 7. Connect to Database
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Error Handling
    if ($is_local) {
        die("<h3>Local Connection Failed:</h3>" . $e->getMessage());
    } else {
        die("<h3>Online Database Connection Failed:</h3>Please check if your database credentials in config.php match your hosting Control Panel.");
    }
}

// 8. Error Reporting
if ($is_local) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Africa/Mogadishu');
?>