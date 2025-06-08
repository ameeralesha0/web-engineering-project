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
    <?php 
    if ($role == 'student') {
        include('sidebar_student.php');
    } elseif ($role == 'advisor') {
        include('sidebar_advisor.php');
    } elseif ($role == 'coordinator') {
        include('sidebar_coordinator.php');
    } else {
        include('login.php'); 
    }
    ?>
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
