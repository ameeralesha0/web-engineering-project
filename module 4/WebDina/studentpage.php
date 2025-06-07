<?php
session_start();

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
    <link rel="stylesheet" href="studentpage.css">
</head>
<body>
<header>
    <img src="petakom.jpg" alt="MyPetakom Logo" class="logo">
    <h2>Welcome, <?php echo ucfirst($_SESSION['user']); ?>!</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
</header>

<div class="dashboard-container">
    <div class="button-group">
        <button class="dashboard-btn" onclick="location.href='profile.php'">View Profile</button>
        <button class="dashboard-btn" onclick="location.href='membership.php'">Manage Membership</button>
        <button class="dashboard-btn" onclick="location.href='student_dashboard.php'">Dashboard</button>
        <button class="dashboard-btn" onclick="location.href='view_merits.php'">View Merits</button>
        <button class="dashboard-btn" onclick="location.href='claim_merit.php'">Claim Missing Merit</button>
    </div>
</div>
</body>
</html>