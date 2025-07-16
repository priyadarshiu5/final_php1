<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'raja');
define('DB_PASSWORD', 'Raja789@');
define('DB_NAME', 'college_club');

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
