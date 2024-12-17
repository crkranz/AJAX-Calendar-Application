<?php
session_start();
require 'database.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the event ID from the request
    $event_id = (int) $_POST['event_id'];  // Use 'event_id' here to match AJAX request

    // Validate event ID
    if (empty($event_id)) {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid event ID."
        ));
        exit;
    }

    // Prepare the SQL query to delete the event
    $stmt = $mysqli->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        echo json_encode(array(
            "success" => false,
            "message" => "Database error: " . $mysqli->error
        ));
        exit;
    }

    // Bind parameters
    $user_id = $_SESSION['user_id'];
    $stmt->bind_param("ii", $event_id, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(array("success" => true, "message" => "Event deleted successfully."));
        } else {
            echo json_encode(array("success" => false, "message" => "No event found to delete."));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Error deleting event: " . $stmt->error));
    }

    // Close the statement and connection
    $stmt->close();
    $mysqli->close();
    exit;
}
