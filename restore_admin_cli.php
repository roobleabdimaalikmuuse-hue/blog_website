<?php
/**
 * Restore Admin Script (CLI Friendly)
 * Forces local connection to fix the issue where user deleted the admin.
 */

// Force Local Settings since we are running via CLI
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

echo "Attempting to connect to Local Database...\n";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "Connected successfully.\n\n";

    // 1. Check existing users in 'admins' table
    echo "Checking 'admins' table...\n";
    $stmt = $pdo->query("SELECT * FROM admins");
    $admins = $stmt->fetchAll();

    if (count($admins) > 0) {
        echo "Found " . count($admins) . " admin(s) in the database:\n";
        foreach ($admins as $admin) {
            echo "- ID: {$admin['id']}, Username: {$admin['username']}, Email: {$admin['email']}\n";
        }
        echo "\nDo you want to reset the password for 'Admin'? (We will update it to 'admin1122')\n";
    } else {
        echo "⚠️ No admins found! The table is empty.\n";
    }

    // 2. Restore/Reset Admin
    $username = 'Admin';
    $email = 'admin@kulmiye.com';
    $password_plain = 'admin1122';
    $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

    // Check if 'Admin' specifically exists (by username or email)
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing
        echo "Updating password for existing Admin (ID: {$existing['id']}) to '$password_plain'...\n";
        $update = $pdo->prepare("UPDATE admins SET password = ?, username = ?, email = ? WHERE id = ?");
        $update->execute([$password_hash, $username, $email, $existing['id']]);
        echo "✅ Password updated successfully.\n";
    } else {
        // Insert new
        echo "Creating new Admin user...\n";
        echo "Username: $username\n";
        echo "Email: $email\n";
        echo "Password: $password_plain\n";

        $insert = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
        $insert->execute([$username, $email, $password_hash]);
        echo "✅ Admin user created successfully.\n";
    }

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>