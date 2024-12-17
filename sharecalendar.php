<?php
session_start();
require 'database.php';
header("Content-Type: application/json");


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array("success" => false, "message" => "User not logged in."));
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Check if the username is provided
if (!isset($_POST['username'])) {
    echo json_encode(array("success" => false, "message" => "No username provided."));
    exit;
}

$target_username = trim($_POST['username']);

// Fetch target user's ID based on the provided username
$target_user_stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
if (!$target_user_stmt) {
    echo json_encode(array("success" => false, "message" => "Database error: " . $mysqli->error));
    exit;
}

$target_user_stmt->bind_param("s", $target_username);
$target_user_stmt->execute();
$target_user_stmt->store_result();

if ($target_user_stmt->num_rows == 0) {
    echo json_encode(array("success" => false, "message" => "Target user not found."));
    exit;
}

$target_user_stmt->bind_result($target_user_id);
$target_user_stmt->fetch();
$target_user_stmt->close();

// Fetch events for the current user
$events_stmt = $mysqli->prepare("SELECT title, date, time, category FROM events WHERE user_id = ?");
if (!$events_stmt) {
    echo json_encode(array("success" => false, "message" => "Database error: " . $mysqli->error));
    exit;
}

$events_stmt->bind_param("i", $current_user_id);
$events_stmt->execute();
$events_stmt->store_result();
$events_stmt->bind_result($title, $date, $time, $category);

// Check if events exist for the current user
if ($events_stmt->num_rows == 0) {
    echo json_encode(array("success" => false, "message" => "No events found for the current user."));
    exit;
}

// Prepare insert statement to share events with the target user
$insert_stmt = $mysqli->prepare("INSERT INTO events (user_id, title, date, time, category) VALUES (?, ?, ?, ?, ?)");
if (!$insert_stmt) {
    echo json_encode(array("success" => false, "message" => "Database error: " . $mysqli->error));
    exit;
}

// Insert each event for the target user
while ($events_stmt->fetch()) {
    $insert_stmt->bind_param("issss", $target_user_id, $title, $date, $time, $category);
    if (!$insert_stmt->execute()) {
        echo json_encode(array("success" => false, "message" => "Error sharing event: " . $insert_stmt->error));
        exit;
    }
}

// Close statements and connection
$events_stmt->close();
$insert_stmt->close();
$mysqli->close();

// Return success message
echo json_encode(array("success" => true, "message" => "Events shared successfully."));
