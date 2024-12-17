<?php
ini_set("session.cookie_httponly", 1);
session_start();
include 'session_security.php';
require 'database.php';
header("Content-Type: application/json");

// Ensure variables are set and filter inputs
$username = trim($_POST['user']);
$pwd_guess = trim($_POST['pass_guess']);

// Prepare the SQL query
$stmt = $mysqli->prepare("SELECT id, username, password FROM users WHERE username = ?");

// Bind the parameter
$stmt->bind_param('s', $username);
$stmt->execute();

// Bind the results
$stmt->bind_result($user_id, $user_name, $stored_password);
$stmt->fetch();
$stmt->close();

// Compare the submitted password to the stored password
if ($user_name && password_verify($pwd_guess, $stored_password)) {
    // Login succeeded!
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $user_name;

    // Generate CSRF token
    $_SESSION['token'] = bin2hex(random_bytes(32));

    // Respond with success
    echo json_encode(array(
        "success" => true,
        "token" => $_SESSION['token']
    ));
    exit;
} else {
    // Login failed; respond with failure
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid username or password."
    ));
    exit;
}
