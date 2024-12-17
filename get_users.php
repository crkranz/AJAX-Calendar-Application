<?php
session_start();
require 'database.php';
header("Content-Type: application/json"); // Sending a JSON response

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "User not logged in."
    ));
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare the statement to get all usernames except the current user's
$stmt = $mysqli->prepare("SELECT username FROM users WHERE id != ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error: " . $mysqli->error
    ));
    exit;
}

// Bind the current user's ID to exclude it from the results
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$users = array();

// Fetch and store the list of usernames only
while ($row = $result->fetch_assoc()) {
    $users[] = array(
        "username" => $row['username']
    );
}

echo json_encode(array(
    "success" => true,
    "users" => $users
));

// Close the statement and database connection
$stmt->close();
$mysqli->close();
