<?php 
  session_start(); 
  header("Cache-Control: no-cache, no-store, must-revalidate");
  header("Pragma: no-cache");
  header("Expires: 0");
  
  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
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
    <!-- logged in user information -->
    <img src="logo-petakom.png" alt="MyPetakom Logo" class="logo">
    <?php  if (isset($_SESSION['username'])) : ?>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
    <?php endif ?>
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
