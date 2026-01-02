<?php
/**
 * Database Connection Test
 * This file tests if the database connection is working properly
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Test - Kulmiye Blog</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .test-item {
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            border-left: 4px solid #ddd;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .links {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Database Connection Test</h1>";

// Test 1: MySQL Extension
echo "<div class='test-item " . (extension_loaded('pdo_mysql') ? 'success' : 'error') . "'>
        <span class='label'>✓ PDO MySQL Extension:</span> " . 
        (extension_loaded('pdo_mysql') ? 'Installed ✓' : 'Not Installed ✗') . 
      "</div>";

// Test 2: Database Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    echo "<div class='test-item success'>
            <span class='label'>✓ Database Connection:</span> Successfully connected to 'blog_db' ✓
          </div>";
    
    // Test 3: Check Tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='test-item success'>
            <span class='label'>✓ Database Tables:</span> Found " . count($tables) . " tables ✓
            <table>
                <tr><th>Table Name</th></tr>";
    foreach ($tables as $table) {
        echo "<tr><td>$table</td></tr>";
    }
    echo "</table></div>";
    
    // Test 4: Check Admin Account
    $stmt = $pdo->query("SELECT username, email FROM admins LIMIT 1");
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<div class='test-item success'>
                <span class='label'>✓ Admin Account:</span> Found admin user<br>
                Username: <strong>{$admin['username']}</strong><br>
                Email: <strong>{$admin['email']}</strong>
              </div>";
    }
    
    // Test 5: Check Posts
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM posts");
    $postCount = $stmt->fetch()['count'];
    
    echo "<div class='test-item success'>
            <span class='label'>✓ Blog Posts:</span> Found {$postCount} posts in database ✓
          </div>";
    
    // Test 6: Check Users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    
    echo "<div class='test-item success'>
            <span class='label'>✓ Registered Users:</span> Found {$userCount} users in database ✓
          </div>";
    
    echo "<div class='test-item info'>
            <span class='label'>ℹ Database Status:</span> Everything is working perfectly! ✓
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='test-item error'>
            <span class='label'>✗ Database Connection Error:</span><br>
            " . htmlspecialchars($e->getMessage()) . "
          </div>";
    
    echo "<div class='test-item info'>
            <span class='label'>💡 Solution:</span>
            <ol>
                <li>Make sure MySQL is running in XAMPP Control Panel</li>
                <li>Create database 'blog_db' in phpMyAdmin</li>
                <li>Import the SQL file from: database/blog_db.sql</li>
            </ol>
          </div>";
}

// Links Section
echo "<div class='links'>
        <h3 style='margin-bottom: 15px;'>📌 Quick Links:</h3>
        <a href='http://localhost/Blog_website/public/index.php' class='btn'>🏠 Public Homepage</a>
        <a href='http://localhost/Blog_website/admin/login.php' class='btn'>🔐 Admin Login</a>
        <a href='http://localhost/phpmyadmin' class='btn'>💾 phpMyAdmin</a>
      </div>";

echo "<div class='test-item info' style='margin-top: 20px;'>
        <span class='label'>📋 Default Login Credentials:</span><br>
        <strong>Admin Panel:</strong><br>
        Username: <code>Admin</code><br>
        Password: <code>admin1122</code>
      </div>";

echo "</div></body></html>";
?>
