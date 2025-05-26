<?php 
session_start(); 

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
}
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['role']);
    header("location: login.php");
}

$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
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
    <!-- role-based sidebar buttons omitted for brevity (keep as before) -->
    <form action="logout.php">
        <button type="submit" class="red-btn">Logout</button>
    </form>
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
    <a href="javascript:history.back()"><button type="button" class="back-btn">Back</button></a>
</div>
</body>
</html>
