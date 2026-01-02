<?php
require_once 'includes/config.php';

// Password-ka cusub: admin1122
$new_password = 'admin1122';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Update admin password
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = 'Admin'");
    $stmt->execute([$hashed_password]);

    echo "<h1>✅ Guul! Password-ka waa la bedelay.</h1>";
    echo "<p>Admin Password-ka cusub waa: <strong>admin1122</strong></p>";
    echo "<p>Fadlan hadda isku day inaad gasho.</p>";
    echo "<br><a href='public/login.php' style='padding:10px; background:blue; color:white; text-decoration:none;'>Tag Login Page >></a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>