<?php
/**
 * Kulmiye Blog - Admin Logout
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Clear admin session
session_unset();
session_destroy();

// Start new session for flash message
session_start();
set_flash('success', 'You have been logged out successfully.');

redirect('/public/login.php');
