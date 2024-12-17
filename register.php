<?php
session_start();
require 'database.php';
header("Content-Type: application/json");

// Ensure that the variables (username and password) are set and filter the inputs
$user_input = isset($_POST['user']) ? trim($_POST['user']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Check if both fields are not empty
if (empty($user_input) || empty($password) || empty($confirm_password)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Username and password cannot be empty"
    ));
    exit;
}

// Check if passwords match
if ($password !== $confirm_password) {
    echo json_encode(array(
        "success" => false,
        "message" => "Passwords do not match"
    ));
    exit;
}

// Check to see if the username is available
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed"
    ));
    exit;
}

// Bind parameters and execute
$stmt->bind_param('s', $user_input);
$stmt->execute();
$stmt->store_result(); // Store the result to check the number of rows

// Check if the username already exists
if ($stmt->num_rows == 0) {
    // Username is available, proceed to create the user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Attempt to add the new user into the database
    $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if (!$stmt) {
        echo json_encode(array(
            "success" => false,
            "message" => "Query preparation failed"
        ));
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param('ss', $user_input, $hashed_password);
    $stmt->execute();
    $stmt->close();

    echo json_encode(array(
        "success" => true,
        "message" => "User registered successfully"
    ));
    exit;
} else {
    // Username is not available
    echo json_encode(array(
        "success" => false,
        "message" => "Username is not available"
    ));
    exit;
}
