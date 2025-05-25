<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$user = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
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
    <form action="<?= htmlspecialchars($previous) ?>">
        <button type="submit" class="blue-btn">Main</button>
    </form>
    <form action="profile.php">
        <button type="submit" class="blue-btn">Manage Account</button>
    </form>

    <!-- Display these buttons only if the role is 'student' -->
    <?php if ($role === 'student'): ?>
        <form action="membership.php">
            <button type="submit" class="blue-btn">Manage Membership</button>
        </form>
        <form action="merit.php">
            <button type="submit" class="blue-btn">Manage Merit</button>
        </form>
        <form action="viewmerit.php">
            <button type="submit" class="blue-btn">View Merit</button>
        </form>
        <form action="missingmerit.php">
            <button type="submit" class="blue-btn">Claim Messing Merit</button>
        </form>
    <?php endif; ?>  

    <!-- Display these buttons only if the role is 'coordinator' -->
    <?php if ($role === 'coordinator'): ?>
        <form action="approve_membership.php">
            <button type="submit" class="blue-btn">Manage Membership</button>
        </form>
        <form action="merit.php">
            <button type="submit" class="blue-btn">Manage Merit</button>
        </form>
        <form action="studentpage.php">
            <button type="submit" class="blue-btn">Student Page</button>
        </form>
        <form action="report.php">
            <button type="submit" class="blue-btn">Generate report</button>
        </form>
    <?php endif; ?>

    <!-- Display these buttons only if the role is 'advisor' -->
    <?php if ($role === 'advisor'): ?>
        <form action="registration.php">
            <button type="submit" class="blue-btn">Registration</button>
        </form>
        <form action="merit.php">
            <button type="submit" class="blue-btn">Manage Merit</button>
        </form>
        <form action="eventcommittee.php">
            <button type="submit" class="blue-btn">Event Committee</button>
        </form>
        <form action="qrcode.php">
            <button type="submit" class="blue-btn">Qr Code</button>
        </form>
    <?php endif; ?> 

    <form action="coordinatordashboard.php">
            <button type="submit" class="blue-btn">Dashboard</button>
        </form>
    <form action="logout.php">
        <button type="submit" class="red-btn">Logout</button>
    </form>
</div>

<div class="main-content">
    <h2>Account Details</h2>
    <div class="form-group">
        <label>Username:</label>
        <input type="text" value="<?= htmlspecialchars($user) ?>" readonly>
    </div>
    <div class="form-group">
        <label>Email Address:</label>
        <input type="email" value="<?= htmlspecialchars($email) ?>" readonly>
    </div>
    <div class="form-group">
        <label>Role:</label>
        <input type="text" value="<?= htmlspecialchars($role) ?>" readonly>
    </div>
    <a href="<?= htmlspecialchars($previous) ?>"><button type="button" class="back-btn">Back</button></a>
</div>
</body>
</html>
