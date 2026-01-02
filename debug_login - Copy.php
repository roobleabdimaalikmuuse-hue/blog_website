<?php
/**
 * Debug Login Script
 * This checks why login is failing
 */
require_once 'includes/config.php';

// Test Credentials
$test_user = 'Admin';
$test_pass = 'admin1122';

echo "<h2>🔍 Debugging Login for User: '$test_user'</h2>";

try {
    // 1. Check Database Record
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$test_user]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<h3 style='color:red'>❌ User Not Found!</h3>";
        echo "<p>Searching for username '$test_user' returned no results.</p>";

        // Show what IS in the database
        $all = $pdo->query("SELECT id, username FROM admins")->fetchAll();
        echo "<p>Here are the available users in 'admins' table:</p><pre>";
        print_r($all);
        echo "</pre>";

    } else {
        echo "<h3 style='color:green'>✅ User Found!</h3>";
        echo "<p>Details:</p>";
        echo "<ul>";
        echo "<li>ID: " . $user['id'] . "</li>";
        echo "<li>Username: '" . $user['username'] . "'</li>";
        echo "<li>Stored Hash: " . substr($user['password'], 0, 20) . "...</li>";
        echo "</ul>";

        // 2. Verify Password
        echo "<h3>🔐 Verifying Password...</h3>";
        echo "<p>Testing password: <strong>$test_pass</strong></p>";

        if (password_verify($test_pass, $user['password'])) {
            echo "<h2 style='color:green'>✅ SUCCESS! Password Matches.</h2>";
            echo "<p>The login issues might be session related or form input related.</p>";
        } else {
            echo "<h2 style='color:red'>❌ FAILED! Password Mismatch.</h2>";
            echo "<p>The password '$test_pass' does NOT match the stored hash.</p>";

            // Re-hash check
            $new_hash = password_hash($test_pass, PASSWORD_DEFAULT);
            echo "<p>New Hash would look like: <br><code>$new_hash</code></p>";
        }
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
?>