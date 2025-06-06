<?php 
session_start(); 
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
    exit();
}

$role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
$email = $_SESSION['email'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Details - MyPetakom</title>
    <link rel="stylesheet" href="styleaccountdetails.css">
</head>
<body>
<div class="sidebar">
    <img src="logo-petakom.png" alt="MyPetakomLogo">

    <!-- Main Button Based on Role -->
    <?php if ($role === 'student'): ?>
        <form action="studentpage.php"><button type="submit" class="blue-btn">Main</button></form>
    <?php elseif ($role === 'advisor'): ?>
        <form action="advisorpage.php"><button type="submit" class="blue-btn">Main</button></form>
    <?php elseif ($role === 'coordinator'): ?>
        <form action="coordinatorpage.php"><button type="submit" class="blue-btn">Main</button></form>
    <?php endif; ?>

    <form action="profile.php"><button type="submit" class="blue-btn">Manage Account</button></form>

    <!-- Student buttons -->
    <?php if ($role === 'student'): ?>
        <form action="membership.php"><button type="submit" class="blue-btn">Manage Membership</button></form>
        <form action="merit.php"><button type="submit" class="blue-btn">Manage Merit</button></form>
        <form action="viewmerit.php"><button type="submit" class="blue-btn">View Merit</button></form>
        <form action="missingmerit.php"><button type="submit" class="blue-btn">Claim Missing Merit</button></form>
    <?php endif; ?>

    <!-- Coordinator buttons -->
    <?php if ($role === 'coordinator'): ?>
        <form action="approve_membership.php"><button type="submit" class="blue-btn">Manage Membership</button></form>
        <form action="merit.php"><button type="submit" class="blue-btn">Manage Merit</button></form>
        <form action="studentpage.php"><button type="submit" class="blue-btn">Student Page</button></form>
        <form action="report.php"><button type="submit" class="blue-btn">Generate Report</button></form>
    <?php endif; ?>

    <!-- Advisor buttons -->
    <?php if ($role === 'advisor'): ?>
        <form action="registration.php"><button type="submit" class="blue-btn">Registration</button></form>
        <form action="merit.php"><button type="submit" class="blue-btn">Manage Merit</button></form>
        <form action="eventcommittee.php"><button type="submit" class="blue-btn">Event Committee</button></form>
        <form action="qrcode.php"><button type="submit" class="blue-btn">QR Code</button></form>
    <?php endif; ?>

    <!-- Shared dashboard and logout -->
    <form action="coordinatordashboard.php"><button type="submit" class="blue-btn">Dashboard</button></form>
    <form action="logout.php"><button type="submit" class="red-btn">Logout</button></form>
</div>

<div class="main-content">
    <h2>Account Details</h2>
    <div class="form-group">
        <label>Username:</label>
        <input type="text" value="<?= htmlspecialchars($username) ?>" readonly>
    </div>
    <div class="form-group">
        <label>Email Address:</label>
        <input type="email" value="<?= htmlspecialchars($email) ?>" readonly>
    </div>
    <div class="form-group">
        <label>Role:</label>
        <input type="text" value="<?= htmlspecialchars($role) ?>" readonly>
    </div>
    <button type="button" class="back-btn" onclick="history.back()">Back</button>
</div>
</body>
</html>
