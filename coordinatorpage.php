<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
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
        <button class="dashboard-btn" onclick="location.href='accountdetails.php'">View Profile</button>
        <button class="dashboard-btn" onclick="location.href='approve_membership.php'">Approve Membership</button>
        <button class="dashboard-btn" onclick="location.href='manage_merit.php'">Manage Merit</button>
        <button class="dashboard-btn" onclick="location.href='studentpage.php'">Student Page</button>
        <button class="dashboard-btn" onclick="location.href='report.php'">generate report</button>
    </div>
</div>

</body>
</html>
