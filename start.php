<!DOCTYPE html>
<html lang="so">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulmiye Blog System - Navigation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 50px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo h1 {
            font-size: 3em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .logo p {
            color: #666;
            font-size: 1.1em;
        }

        .status {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            color: #155724;
        }

        .status.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }

        .status.error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .link-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-decoration: none;
            color: #333;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .link-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            border-color: #667eea;
        }

        .link-card .icon {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .link-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .link-card p {
            color: #666;
            font-size: 0.9em;
            line-height: 1.5;
        }

        .link-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .link-card.primary h3,
        .link-card.primary p {
            color: white;
        }

        .credentials {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
        }

        .credentials h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .cred-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .cred-item strong {
            color: #667eea;
        }

        .cred-item code {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            color: #666;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            .logo h1 {
                font-size: 2em;
            }

            .links-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>🌟 Kulmiye</h1>
            <p>Blog Management System</p>
        </div>

        <?php
        // Check if MySQL is running
        $mysqlRunning = false;
        $dbConnected = false;

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=blog_db;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $mysqlRunning = true;
            $dbConnected = true;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                $mysqlRunning = true;
                $dbConnected = false;
            }
        }

        if ($dbConnected) {
            echo '<div class="status">
                    ✅ <strong>System Status:</strong> Database connected successfully!
                  </div>';
        } elseif ($mysqlRunning) {
            echo '<div class="status warning">
                    ⚠️ <strong>Warning:</strong> MySQL is running but database "blog_db" not found. Please import the SQL file.
                  </div>';
        } else {
            echo '<div class="status error">
                    ❌ <strong>Error:</strong> MySQL is not running. Please start MySQL in XAMPP Control Panel.
                  </div>';
        }
        ?>

        <div class="links-grid">
            <a href="public/index.php" class="link-card primary">
                <div class="icon">🏠</div>
                <h3>Public Website</h3>
                <p>View the blog homepage and browse posts</p>
            </a>

            <a href="admin/login.php" class="link-card">
                <div class="icon">🔐</div>
                <h3>Admin Panel</h3>
                <p>Login to manage posts, users, and comments</p>
            </a>

            <a href="test_db.php" class="link-card">
                <div class="icon">🔍</div>
                <h3>Database Test</h3>
                <p>Check database connection and tables</p>
            </a>

            <a href="http://localhost/phpmyadmin" class="link-card" target="_blank">
                <div class="icon">💾</div>
                <h3>phpMyAdmin</h3>
                <p>Manage database directly</p>
            </a>
        </div>

        <div class="credentials">
            <h3>🔑 Default Login Credentials</h3>
            <div class="cred-item">
                <strong>Admin Username:</strong>
                <code>Admin</code>
            </div>
            <div class="cred-item">
                <strong>Admin Password:</strong>
                <code>admin1122</code>
            </div>
        </div>

        <div class="footer">
            <p>📚 <strong>Documentation:</strong> Check README.md for full installation guide</p>
            <p style="margin-top: 10px; color: #999;">Built with ❤️ using PHP, MySQL & Bootstrap 5</p>
        </div>
    </div>
</body>

</html>