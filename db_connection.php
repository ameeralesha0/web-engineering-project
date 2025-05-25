<?php
$host = "localhost";
$username = "root"; 
$password = "";
$database = "mypetakom";

// Report MySQLi errors for debugging (disable in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
