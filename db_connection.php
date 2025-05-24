<?php
$host = "localhost";
$username = "root"; // update if different
$password = ""; // update if different
$database = "petakom_system";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
