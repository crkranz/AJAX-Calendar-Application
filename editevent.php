<?php
session_start();
require 'database.php';
header("Content-Type: application/json");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "User not logged in."
    ));
    exit;
}

//CSRF Token
if (!isset($_POST['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Request forgery detected."
    ));
    exit;
}

// Ensure the event ID, title, time and category is set and filter input
$event_id = (int)$_POST['id'];
$title = htmlentities(trim($_POST['title']));
$time = trim($_POST['time']);
$category = trim($_POST['category']);

// Validate event data
if (empty($title) || empty($time) || empty($category)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid event data."
    ));
    exit;
}

// Debugging log
error_log("Event ID: $event_id, Title: $title, Time: $time, Category: $category");

// Prepare the SQL query to update the event
$stmt = $mysqli->prepare("UPDATE events SET title = ?, time = ?, category = ? WHERE id = ? AND user_id = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error: " . $mysqli->error
    ));
    exit;
}

// Bind parameters
$user_id = $_SESSION['user_id'];
$stmt->bind_param("ssssi", $title, $time, $category, $event_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(array(
        "success" => true,
        "message" => "Event updated successfully.",
        "title" => $title,
        "time" => $time,
        "category" => $category
    ));
} else {
    echo json_encode(array("success" => false, "message" => "Error updating event: " . $stmt->error));
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
