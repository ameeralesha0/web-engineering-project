<?php
include('server.php'); // assumes $db is your mysqli connection

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Map role to table and columns
$role_map = [
    'student' => [
        'table' => 'student',
        'username_col' => 'student_username',
        'email_col' => 'student_email',
    ],
    'advisor' => [
        'table' => 'advisor',
        'username_col' => 'advisor_username',
        'email_col' => 'advisor_email',
    ],
    'coordinator' => [
        'table' => 'petakom_coordinator',
        'username_col' => 'staff_username',
        'email_col' => 'staff_email',
    ],
];

if (!array_key_exists($role, $role_map)) {
    die("Invalid user role.");
}

$table = $role_map[$role]['table'];
$username_col = $role_map[$role]['username_col'];
$email_col = $role_map[$role]['email_col'];

// Prepare query safely
$query = "SELECT * FROM $table WHERE $username_col = ? LIMIT 1";
$stmt = $db->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $db->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $email = $row[$email_col];
    $_SESSION['email'] = $email;
    $currentUsername = $username; // for display in form
} else {
    die("User not found in the $table table.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Account - MyPetakom</title>
    <link rel="stylesheet" href="styleprofile.css" />
</head>
<body>
    <div class="sidebar">
        <img src="logo-petakom.png" alt="MyPetakomLogo" class="logo" />

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
                <button type="submit" class="blue-btn">Claim Missing Merit</button>
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
                <button type="submit" class="blue-btn">Generate Report</button>
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
                <button type="submit" class="blue-btn">QR Code</button>
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
        <form method="post" action="update_profile.php"> <!-- Separate update script recommended -->
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required />
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($currentUsername) ?>" required />
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password" />
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
        <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </div>
</body>
</html>
