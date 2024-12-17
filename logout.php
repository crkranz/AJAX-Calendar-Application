<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    exit; // User is not logged in
}

require 'database.php';

// Check for CSRF token validity
if (!isset($_POST['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Request forgery detected."
    ));
    exit;
}

// Unset all session variables and destroy the session
session_unset();
session_destroy();

// Respond with success
echo json_encode(array("success" => true, "message" => "Logged out successfully."));
exit;
