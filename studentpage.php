<?php
session_start();
$host = "127.0.0.1:3307";
$username = "root";      // use your DB username
$password = "";          // use your DB password
$database = "mypetakom";   // your DB name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - MyPetakom</title>
    <link rel="stylesheet" href="stylepage.css">
</head>
<body>
<header>
    <img src="logo-petakom.png" alt="MyPetakom Logo" class="logo">
    <h2>Welcome, <?php echo ucfirst($_SESSION['user']); ?>!</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
</header>

<div class="dashboard-container">
    <div class="button-group">
        <button class="dashboard-btn" onclick="location.href='profile.php'">View Profile</button>
        <button class="dashboard-btn" onclick="location.href='membership.php'">Manage Membership</button>
        <button class="dashboard-btn" onclick="location.href='manage_merit.php'">Manage Merit</button>
        <button class="dashboard-btn" onclick="location.href='view_merits.php'">View Merits</button>
        <button class="dashboard-btn" onclick="location.href='claim_merit.php'">Claim Missing Merit</button>
    </div>
</div>
</body>
</html>
