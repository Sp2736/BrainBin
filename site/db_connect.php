<?php
// Database config
$DB_HOST = "localhost";
$DB_USER = "root";         // MySQL username
$DB_PASS = "";             // MySQL password
$DB_NAME = "brainbin_data"; // <-- use your new database

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
