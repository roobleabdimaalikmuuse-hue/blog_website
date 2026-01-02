<?php
/**
 * Admin Login Redirect
 * Redirects to the main login page which handles admin authentication
 */
require_once '../includes/config.php';
header('Location: ' . SITE_URL . '/public/login.php');
exit;
?>