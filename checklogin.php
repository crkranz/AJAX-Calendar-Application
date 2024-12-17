<?php
session_start();
require 'database.php';

header("Content-Type: application/json");

// Check if the session is set correctly
if (!isset($_SESSION['user_id'])) {
    header("Location: calendarhome.html");
    exit;
}

// Check if the token is set in the session
if (!isset($_SESSION['token'])) {
    echo json_encode(array("login" => false, "message" => "Session token not found."));
    exit;
}

// Return login status and token
echo json_encode(array("login" => true, "token" => $_SESSION['token']));
exit;
