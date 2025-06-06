<?php
// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mypetakom";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//success message for testing
echo "Database Connected Successfully!";
?>