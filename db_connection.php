<?php
$host = "127.0.0.1:3307";
$username = "root"; // update if different
$password = ""; // update if different
$database = "mypetakom";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>