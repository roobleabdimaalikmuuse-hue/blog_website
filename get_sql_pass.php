<?php
/**
 * Generate Admin Password SQL
 */
$password = 'admin1122';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h1>🔑 Password Fixer</h1>";
echo "<p>Si aad password-ka uga dhigto <strong>$password</strong>, isticmaal SQL-kan:</p>";

echo "<textarea cols='80' rows='5' style='padding:10px; font-size:16px;'>";
echo "UPDATE admins SET password = '$hash' WHERE username = 'Admin';";
echo "</textarea>";

echo "<hr>";
echo "<h3>Tallaabooyinka:</h3>";
echo "<ol>";
echo "<li>Copy garee SQL-ka sare.</li>";
echo "<li>Tag <strong>phpMyAdmin</strong>.</li>";
echo "<li>Guji Database-kaaga.</li>";
echo "<li>Guji <strong>SQL</strong> tab-ka.</li>";
echo "<li>Paste garee code-ka, kadibna guji <strong>Go</strong>.</li>";
echo "</ol>";
?>