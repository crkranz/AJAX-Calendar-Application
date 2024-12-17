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

// CRSF Token
if (!isset($_POST['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Request forgery detected."
    ));
    exit;
}


// Ensure variables are set and filter inputs
$title = htmlentities(trim($_POST['title']));
$year = (int)$_POST['year'];
$month = (int)$_POST['month'];
$day = (int)$_POST['day'];
$time = trim($_POST['time']);
$category = trim($_POST['category']);
$share_username = trim($_POST['username']);


// Validate event data
if (empty($title) || empty($category) || empty($time) || !checkdate($month, $day, $year)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid event data."
    ));
    exit;
}

// Prepare to insert the new event for the current user
$user_id = $_SESSION['user_id'];
$date = "$year-$month-$day";

// Insert event for the current user
$insert_stmt = $mysqli->prepare("INSERT INTO events (user_id, title, date, time, category) VALUES (?, ?, ?, ?, ?)");
if (!$insert_stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error: " . $mysqli->error
    ));
    exit;
}

// Bind parameters for insertion
$insert_stmt->bind_param("issss", $user_id, $title, $date, $time, $category);

// Execute the insert statement
if ($insert_stmt->execute()) {
    $event_id = $mysqli->insert_id; // Get the ID of the newly inserted event

    // Check if a username is provided for sharing
    if (!empty($share_username)) {
        // Fetch target user's ID based on the provided username
        $target_user_stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        if (!$target_user_stmt) {
            echo json_encode(array("success" => false, "message" => "Database error: " . $mysqli->error));
            exit;
        }

        $target_user_stmt->bind_param("s", $share_username);
        $target_user_stmt->execute();
        $target_user_stmt->store_result();

        // Check if target user exists
        if ($target_user_stmt->num_rows == 0) {
            echo json_encode(array("success" => false, "message" => "Target user not found."));
            exit;
        }

        $target_user_stmt->bind_result($target_user_id);
        $target_user_stmt->fetch();
        $target_user_stmt->close();

        // Share the event with the target user
        $share_stmt = $mysqli->prepare("INSERT INTO events (user_id, title, date, time, category) VALUES (?, ?, ?, ?, ?)");
        if (!$share_stmt) {
            echo json_encode(array("success" => false, "message" => "Database error: " . $mysqli->error));
            exit;
        }

        // Bind parameters for sharing
        $share_stmt->bind_param("issss", $target_user_id, $title, $date, $time, $category);
        if (!$share_stmt->execute()) {
            echo json_encode(array("success" => false, "message" => "Error sharing event: " . $share_stmt->error));
            exit;
        }

        // Close the share statement
        $share_stmt->close();
    }

    // Both events added successfully
    echo json_encode(array("success" => true, "message" => "Event created successfully for you and shared with $share_username."));
} else {
    echo json_encode(array("success" => false, "message" => "Error adding event: " . $insert_stmt->error));
}

// Close the initial insert statement and database connection
$insert_stmt->close();
$mysqli->close();
