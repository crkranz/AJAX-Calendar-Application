<?php
session_start();

// Create a new MySQLi connection
$mysqli = new mysqli('localhost', 'crkranz', 'Boston2878?!?', 'Calendar');

// Check for connection errors
if ($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit; // Stop execution on failure
}
