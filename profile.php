<?php
include('server.php'); // Make sure this includes DB connection $db

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$email = null;

// Determine table name based on role
$table = '';
if ($role === 'student') {
    $table = 'student';
} elseif ($role === 'advisor') {
    $table = 'advisor';
} elseif ($role === 'coordinator') {
    $table = 'coordinator';
} else {
    die("Invalid user role.");
}

// Fetch user data from the appropriate table
$query = "SELECT * FROM $table WHERE username = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $email = $row['email'];
    $_SESSION['email'] = $email; // Store it in session for reuse
} else {
    die("User not found in the $table table.");
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Manage Account - MyPetakom</title>
    <link rel="stylesheet" href="styleprofile.css">
</head>
<body>
    <div class="sidebar">
        <img src="logo-petakom.png" alt="MyPetakomLogo" class="logo">

        <form action="<?= htmlspecialchars($dashboard ?? '#') ?>" method="get">
            <button type="submit" class="blue-btn">Main</button>
        </form>

        <form action="accountdetails.php">
            <button type="submit" class="blue-btn">Account Details</button>
        </form>

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
        <h2>Manage Account</h2>
        <form method="post">
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($currentUsername) ?>" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password">
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
</body>
</html>
