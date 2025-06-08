<?php
include('db_connection.php');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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
