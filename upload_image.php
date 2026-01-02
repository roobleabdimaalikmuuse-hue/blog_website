<?php
/**
 * Kulmiye Blog - TinyMCE Image Upload Handler
 */

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Ensure admin is logged in
if (!is_admin_logged_in()) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// Allowed origins
$accepted_origins = [
    "http://localhost",
    "http://127.0.0.1",
    SITE_URL
];

// Images upload path
$imageFolder = "../assets/images/uploads/";

reset($_FILES);
$temp = current($_FILES);

if (is_uploaded_file($temp['tmp_name'])) {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Same-origin requests won't set an origin. If the origin is set, it must be valid.
        if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        } else {
            // For development flexibility, if it looks like localhost, let it slide or debug
            // But strict 403 is why it failed. 
            // Let's rely on the expanded $accepted_origins list.
             header("HTTP/1.1 403 Forbidden");
             echo "Origin Denied: " . $_SERVER['HTTP_ORIGIN']; // feedback
             return;
        }
    }

    /*
      if your server is under strict domain policy check, 
      you may need to allow X-Requested-With header
    */
    header("Access-Control-Allow-Credentials: true");
    header("P3P: CP=\"There is no P3P policy.\"");

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.1 400 Invalid file name.");
        return;
    }

    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), ["gif", "jpg", "jpeg", "png", "webp"])) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    $filetowrite = $imageFolder . $temp['name'];
    
    // Check if file exists, rename if needed
    $filename = pathinfo($temp['name'], PATHINFO_FILENAME);
    $extension = pathinfo($temp['name'], PATHINFO_EXTENSION);
    $counter = 1;
    while (file_exists($filetowrite)) {
        $filetowrite = $imageFolder . $filename . '_' . $counter . '.' . $extension;
        $counter++;
    }

    if (move_uploaded_file($temp['tmp_name'], $filetowrite)) {
        // Return JSON response with location
        // We need the public URL for the image
        // $filetowrite is relative for PHP, we need URL
        
        // Assuming $filetowrite is "../assets/images/uploads/filename.jpg"
        // We need SITE_URL . "/assets/images/uploads/filename.jpg"
        
        // Clean path to remove "../"
        $public_path = str_replace('../', '/', $filetowrite);
        
        echo json_encode(array('location' => SITE_URL . $public_path));
    } else {
        header("HTTP/1.1 500 Server Error");
    }
} else {
    header("HTTP/1.1 500 Server Error");
}
?>
