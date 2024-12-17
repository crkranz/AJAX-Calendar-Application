<?php
session_start();

header("Content-Type: application/json");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "User not logged in"
    ));
    exit;
}

require 'database.php'; // Include your database connection

// Retrieve user ID from the session
$user_id = $_SESSION['user_id'];

// Get the year and month from the query parameters
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Prepare the SQL statement to fetch title, user_id, id, date, time, and category from the events table
$stmt = $mysqli->prepare("SELECT title, user_id, id, date, time, category FROM events WHERE user_id = ? AND YEAR(date) = ? AND MONTH(date) = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => $mysqli->error
    ));
    exit;
}

$stmt->bind_param("iii", $user_id, $year, $month);
$stmt->execute();
$stmt->bind_result($title, $user_id, $id, $date, $time, $category); // Bind the result variables

// Fetch the results and store them in an array
$events = array();
while ($stmt->fetch()) {
    $events[] = array(
        'title' => $title,
        'user_id' => $user_id,
        'id' => $id,
        'date' => $date,
        'time' => $time,
        'category' => $category
    );
}

// Close the statement
$stmt->close();

// Output the results as a JSON object
echo json_encode(array(
    "success" => true,
    "events" => $events
));
