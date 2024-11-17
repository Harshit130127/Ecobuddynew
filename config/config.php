<?php
// Database connection parameters
$servername = "localhost"; // Server name
$username = "root";         // Default username for XAMPP
$password = "";             // Default password is empty
$dbname = "paid2";         // Your database name

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname); // Use $mysqli here

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

?>